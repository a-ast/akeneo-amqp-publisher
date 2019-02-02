<?php

namespace spec\Aa\AkeneoImport\Import;

use Aa\AkeneoImport\CommandBus\CommandBus;
use Aa\AkeneoImport\CommandBus\CommandBusInterface;
use Aa\AkeneoImport\ImportCommand\CommandCallbacks;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
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
        $commandBus->dispatch(Argument::type(TestCommand::class), Argument::type(CommandCallbacks::class))->shouldBeCalledTimes(2);
        $commandBus->tearDown()->shouldBeCalled();

        $this->import([$command1, $command2]);
    }

    function it_imports_command_from_queue(CommandBus $commandBus, CommandQueueInterface $queue)
    {
        $command1 = new TestCommand('1');
        $command2 = new TestCommand('2');

        $queue->dequeue()->willReturn($command1, $command2, null);

        $commandBus->setUp()->shouldBeCalled();
        $commandBus->dispatch(Argument::type(TestCommand::class), Argument::type(CommandCallbacks::class))->shouldBeCalledTimes(2);
        $commandBus->tearDown()->shouldBeCalled();

        $this->import([$command1, $command2]);
    }

    function it_fails_if_command_is_requeued_more_than_x_times()
    {
        $this->beConstructedWith(new CommandBusSpy());

        $command = new TestCommand('1');

        $this->shouldThrow(CommandHandlerException::class)->during('import', [[$command]]);
    }
}

class CommandBusSpy implements CommandBusInterface
{
    public function dispatch(CommandInterface $command, CommandCallbacks $callbacks = null)
    {
        $callbacks->repeat($command);
    }

    public function setUp(): void {}

    public function tearDown(): void {}
}


