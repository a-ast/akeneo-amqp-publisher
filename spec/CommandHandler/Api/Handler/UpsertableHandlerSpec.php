<?php

namespace spec\Aa\AkeneoImport\CommandHandler\Api\Handler;

use Aa\AkeneoImport\CommandBus\CommandPromise;
use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Aa\AkeneoImport\fixture\TestCommand;
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


    function it_handles_one_command(UpsertableResourceListInterface $api)
    {
        $api->upsertList(Argument::type('array'))->shouldBeCalled()->willReturn([]);

        $this->handle(new CommandPromise(new TestCommand('1'), function() {}));
        $this->tearDown();
    }

    function it_handles_commands_and_sends_data_in_batches(UpsertableResourceListInterface $api)
    {
        $api->upsertList(Argument::type('array'))->shouldBeCalledTimes(3)->willReturn([]);

        $this->handle(new CommandPromise(new TestCommand('1'), function() {}));
        $this->handle(new CommandPromise(new TestCommand('2'), function() {}));
        $this->handle(new CommandPromise(new TestCommand('3'), function() {}));
        $this->handle(new CommandPromise(new TestCommand('4'), function() {}));
        $this->handle(new CommandPromise(new TestCommand('5'), function() {}));
        $this->tearDown();
    }
}
