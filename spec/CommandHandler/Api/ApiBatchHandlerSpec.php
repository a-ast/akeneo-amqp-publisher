<?php

namespace spec\Aa\AkeneoImport\CommandHandler\Api\Api;

use Aa\AkeneoImport\CommandHandler\Api\ApiAdapter\ApiAdapterInterface;
use Aa\AkeneoImport\CommandHandler\Api\ApiBatchHandler;
use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\Response;
use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\ValidatorInterface;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\CommandBatch;
use Aa\AkeneoImport\ImportCommand\CommandBatchInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Exception\RecoverableCommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Product\UpdateOrCreateProduct;
use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Akeneo\Pim\ApiClient\Api\MediaFileApiInterface;
use Akeneo\Pim\ApiClient\Api\ProductApiInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ApiBatchHandlerSpec extends ObjectBehavior
{
    function let(AkeneoPimClientInterface $client,
        NormalizerInterface $normalizer,
        ApiAdapterInterface $apiAdapter,
        ValidatorInterface $validator
    ) {
        $this->beConstructedWith($client, $normalizer, [$apiAdapter], $validator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ApiBatchHandler::class);
    }

    function it_fails_for_empty_command_list()
    {
        $commandBatch = new CommandBatch([]);

        $this
            ->shouldThrow(CommandHandlerException::class)
            ->during('handle', [$commandBatch]);
    }

    function it_selects_an_adapter_and_call_it(
        AkeneoPimClientInterface $client,
        NormalizerInterface $normalizer,
        ApiAdapterInterface $apiAdapter,
        ValidatorInterface $validator,
        ProductApiInterface $api
    ) {
        $commandBatch = new CommandBatch([
            new UpdateOrCreateProduct('1'),
            new UpdateOrCreateProduct('2'),
        ]);

        $normalizer->normalize($commandBatch->getItems())->willReturn([]);

        $client->getProductApi()->willReturn($api);

        $apiAdapter
            ->supportsApi($api)
            ->shouldBeCalled()
            ->willReturn(true);

        $apiResponse = new \ArrayObject([
            new Response(['identifier' => '1', 'status_code' => 201]),
            new Response(['identifier' => '2', 'status_code' => 201]),
        ]);

        $apiAdapter
            ->send($api, [])
            ->willReturn($apiResponse);

        $validator
            ->supportsApi($api, UpdateOrCreateProduct::class)
            ->willReturn(true);

        $validator
            ->validate($apiResponse)
            ->shouldBeCalled();

        $this->handle($commandBatch);
    }

    function it_throws_exception_if_api_adapter_not_found(
        ApiAdapterInterface $apiAdapter
    ) {
        $commandBatch = new CommandBatch([new UpdateOrCreateProduct('1')]);

        $apiAdapter
            ->supportsApi(Argument::any())
            ->shouldBeCalled()
            ->willReturn(false);

        $apiAdapter
            ->send(Argument::any(), Argument::type('array'))
            ->shouldNotBeCalled();

        $this
            ->shouldThrow(CommandHandlerException::class)
            ->during('handle', [$commandBatch]);
    }
}
