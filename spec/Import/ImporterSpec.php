<?php

namespace spec\Aa\AkeneoImport\Import;

use Aa\AkeneoImport\CommandBus\CommandBus;
use Aa\AkeneoImport\Import\Importer;
use Aa\AkeneoImport\ImportCommand\CommandHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Exception\RecoverableCommandHandlerException;
use Aa\AkeneoImport\Queue\CommandQueueInterface;
use Aa\AkeneoImport\Queue\InMemoryQueue;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ImporterSpec extends ObjectBehavior
{
    function let(CommandBus $commandBus, CommandQueueInterface $queue)
    {
        $this->beConstructedWith($commandBus, $queue);
    }

    function it_imports_command_data(CommandBus $commandBus, CommandQueueInterface $queue)
    {
        $command1 = new Command(1);
        $command2 = new Command(2);

        $queue->enqueue($command1)->shouldBeCalled();
        $queue->enqueue($command2)->shouldBeCalled();

        $queue->dequeue()->willReturn($command1, $command2, null);

        $commandBus->dispatch($command1)->shouldBeCalled();
        $commandBus->dispatch($command2)->shouldBeCalled();

        $this->import([$command1, $command2]);
    }

    function it_republishes_failed_recoverable_commands(CommandBus $commandBus, CommandQueueInterface $queue)
    {
        $command1 = new Command(1);
        $command2 = new Command(2);

        $queue->dequeue()->willReturn($command1, $command2, null);

        $exception = new RecoverableCommandHandlerException('Recover', 0, [$command2]);
        $commandBus->dispatch($command1)->shouldBeCalled();
        $commandBus->dispatch($command2)->willThrow($exception);

        $queue->enqueue($command1)->shouldBeCalled();
        $queue->enqueue($command2)->shouldBeCalledTimes(2);

        $this->import([$command1, $command2]);
    }

    function it_does_not_republish_failed_unrecoverable_commands(CommandBus $commandBus, CommandQueueInterface $queue)
    {
        $command = new Command(1);

        $queue->dequeue()->willReturn($command, null);

        $commandBus->dispatch($command)->willThrow(CommandHandlerException::class);
        $queue->enqueue($command)->shouldBeCalledTimes(1);

        $this->import([$command]);
    }

    function it_import_stops_for_all_other_exceptions(CommandBus $commandBus, CommandQueueInterface $queue)
    {
        $command = new Command(1);

        $queue->dequeue()->willReturn($command, null);

        $commandBus->dispatch($command)->willThrow(\Exception::class);

        $this->shouldThrow(\Exception::class)->during('import', [[$command]]);
    }
}

class Command implements CommandInterface
{
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
};

