<?php

namespace spec\Aa\AkeneoImport\CommandHandler\Api\Handler;

use Aa\AkeneoImport\CommandHandler\Api\Handler\CommandAccumulator;
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
        $normalizer
            ->normalize(Argument::type(TestCommand::class), Argument::any(), Argument::any())
            ->will(function(array $commands) {
                $command = $commands[0];

                return array_merge(['identifier' => $command->getProductIdentifier()], $command->getAttributes());
            });

        $this->beConstructedWith($api, 'identifier', $normalizer, 2);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UpsertableHandler::class);
    }

    function it_handles_one_command(UpsertableResourceListInterface $api)
    {
        $api->upsertList(Argument::type('array'))->shouldBeCalled()->willReturn([]);

        $this->handle(new TestCommand('1'));
        $this->tearDown();
    }

    function it_handles_commands_and_sends_data_in_batches(UpsertableResourceListInterface $api)
    {
        $api->upsertList(Argument::type('array'))->shouldBeCalledTimes(3)->willReturn([]);

        $this->handle(new TestCommand('1'));
        $this->handle(new TestCommand('2'));
        $this->handle(new TestCommand('3'));
        $this->handle(new TestCommand('4'));
        $this->handle(new TestCommand('5'));
        $this->tearDown();
    }
}
