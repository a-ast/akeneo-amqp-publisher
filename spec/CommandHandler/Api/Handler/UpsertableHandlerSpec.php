<?php

namespace spec\Aa\AkeneoImport\CommandHandler\Api\Handler;

use Aa\AkeneoImport\CommandHandler\Api\Handler\UpsertableHandler;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Control\FinishImport;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Product\ProductFieldInterface;
use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Aa\AkeneoImport\CommandHandler\Api\Handler\fixture\TestCommand;
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

    function it_handles_one_command(UpsertableResourceListInterface $api, NormalizerInterface $normalizer)
    {
        $normalizer->normalize(Argument::type('array'))->willReturn([['productIdentifier' => 1]]);

        $api->upsertList([
            ['productIdentifier' => 1],

        ])->shouldBeCalled()->willReturn([]);

        $this->handle(new TestCommand('1'));
        $this->handle(new FinishImport());
    }

    function it_handles_commands_and_sends_data_in_batches(UpsertableResourceListInterface $api, NormalizerInterface $normalizer)
    {
        $normalizer->normalize(Argument::type('array'))->willReturn([]);

        $api->upsertList(Argument::type('array'))->shouldBeCalledTimes(2)->willReturn([]);

        $this->handle(new TestCommand(1));
        $this->handle(new TestCommand(2));
        $this->handle(new TestCommand(1));
        $this->handle(new TestCommand(2));
        $this->handle(new TestCommand(3));
        $this->handle(new TestCommand(3));
        $this->handle(new TestCommand(4));
        $this->handle(new FinishImport());
    }

    function it_does_not_send_empty_commands(UpsertableResourceListInterface $api, NormalizerInterface $normalizer)
    {
        $normalizer->normalize(Argument::type('array'))->willReturn([]);
        $api->upsertList(Argument::any())->shouldNotBeCalled();

        $this->handle(new FinishImport());
    }
}
