<?php

namespace spec\Aa\AkeneoImport\CommandHandler\Api\Handler;

use Aa\AkeneoImport\CommandHandler\Api\Handler\UpsertableHandler;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Control\FinishImport;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UpsertableHandlerSpec extends ObjectBehavior
{
    function let(UpsertableResourceListInterface $api, NormalizerInterface $normalizer)
    {
        $this->beConstructedWith($api, $normalizer, 2);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UpsertableHandler::class);
    }

    function it_handles_one_command(UpsertableResourceListInterface $api, NormalizerInterface $normalizer, CommandInterface $command)
    {
        $normalizer->normalize(Argument::type('array'))->willReturn([['productIdentifier' => 1]]);

        $api->upsertList([
            ['productIdentifier' => 1],

        ])->shouldBeCalled()->willReturn([]);

        $this->handle($command);
        $this->handle(new FinishImport());
    }

    function it_handles_commands_and_sends_data_in_batches(UpsertableResourceListInterface $api, NormalizerInterface $normalizer, CommandInterface $command)
    {
        $normalizer->normalize(Argument::type('array'))->willReturn([]);

        $api->upsertList(Argument::type('array'))->shouldBeCalledTimes(2)->willReturn([]);

        $this->handle($command);
        $this->handle($command);
        $this->handle($command);
        $this->handle($command);
        $this->handle(new FinishImport());
    }

    function it_throws_an_exception_if_api_cant_upsert_data(UpsertableResourceListInterface $api, NormalizerInterface $normalizer, CommandInterface $command)
    {
        $api->upsertList(Argument::any())->willReturn([
           ['status_code' => 201, 'message' => 'Ok', 'identifier' => 1],
           ['status_code' => 422, 'message' => 'Wrong command', 'identifier' => 2],
           ['status_code' => 201, 'message' => 'Ok', 'identifier' => 3],
           ['status_code' => 422, 'message' => 'Wrong command', 'identifier' => 4],
        ]);

        $this->handle($command);
        $this->shouldThrow(new CommandHandlerException('Wrong command', ''))->during('handle', [new FinishImport()]);
    }
}
