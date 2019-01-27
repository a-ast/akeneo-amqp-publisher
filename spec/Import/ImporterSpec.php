<?php

namespace spec\Aa\AkeneoImport\Import;

use Aa\AkeneoImport\CommandBus\CommandBus;
use Aa\AkeneoImport\CommandBus\CommandPromise;
use Aa\AkeneoImport\ImportCommand\AsyncCommandHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Exception\RecoverableCommandHandlerException;
use Aa\AkeneoImport\Queue\CommandQueueInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Aa\AkeneoImport\fixture\TestCommand;

class ImporterSpec extends ObjectBehavior
{
    function let(CommandBus $commandBus)
    {
        $this->beConstructedWith($commandBus);
    }

    function it_imports_command_from_array(CommandBus $commandBus)
    {
        $command1 = new TestCommand('1');
        $command2 = new TestCommand('2');

        $commandBus->setUp()->shouldBeCalled();
        $commandBus->dispatch(Argument::type(CommandPromise::class))->shouldBeCalledTimes(2);
        $commandBus->tearDown()->shouldBeCalled();

        $this->import([$command1, $command2]);
    }

    function it_imports_command_from_queue(CommandBus $commandBus, CommandQueueInterface $queue)
    {
        $command1 = new TestCommand('1');
        $command2 = new TestCommand('2');

        $queue->dequeue()->willReturn($command1, $command2, null);

        $commandBus->setUp()->shouldBeCalled();
        $commandBus->dispatch(Argument::type(CommandPromise::class))->shouldBeCalledTimes(2);
        $commandBus->tearDown()->shouldBeCalled();

        $this->import([$command1, $command2]);
    }
}


