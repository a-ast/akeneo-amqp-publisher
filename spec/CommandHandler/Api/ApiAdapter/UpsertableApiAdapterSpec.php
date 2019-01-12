<?php

namespace spec\Aa\AkeneoImport\CommandHandler\Api\ApiAdapter;

use Aa\AkeneoImport\CommandHandler\Api\ApiAdapter\ApiAdapterInterface;
use Aa\AkeneoImport\CommandHandler\Api\ApiAdapter\UpsertableApiAdapter;
use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\Response;
use Aa\AkeneoImport\ImportCommand\CommandBatchInterface;
use Aa\AkeneoImport\ImportCommand\Exception\RecoverableCommandHandlerException;
use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UpsertableApiAdapterSpec extends ObjectBehavior
{
    function let(NormalizerInterface $normalizer)
    {
        $normalizer->normalize(Argument::type('iterable'))->willReturn([]);

        $this->beConstructedWith($normalizer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UpsertableApiAdapter::class);
        $this->shouldBeAnInstanceOf(ApiAdapterInterface::class);
    }

    function it_sends_data_to_api(UpsertableResourceListInterface $api, CommandBatchInterface $commands)
    {
        $commands->getItems()->willReturn([]);

        $apiResponse = new \ArrayObject([
            ['identifier' => '1', 'status_code' => 201],
            ['identifier' => '2', 'status_code' => 201],
        ]);

        $response = [
            new Response(['identifier' => '1', 'status_code' => 201]),
            new Response(['identifier' => '2', 'status_code' => 201]),
        ];

        $api->upsertList([])->willReturn($apiResponse);

        $this->send($api, $commands)->shouldIterateLike($response);
    }

}

interface DummyInterface
{

}
