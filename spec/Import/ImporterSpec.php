<?php

namespace spec\Aa\AkeneoImport\Import;

use Aa\AkeneoImport\CommandBus\CommandBus;
use Aa\AkeneoImport\Import\Importer;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Exception\RecoverableCommandHandlerException;
use Aa\AkeneoImport\Queue\CommandQueueInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ImporterSpec extends ObjectBehavior
{
    function let(CommandBus $commandBus, CommandQueueInterface $queue)
    {
        $this->beConstructedWith($commandBus, $queue);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Importer::class);
    }

    function it_republishes_failed_recoverable_commands(CommandBus $commandBus, CommandQueueInterface $queue)
    {
        $command1 = new class implements CommandInterface {};
        $command2 = new class implements CommandInterface {};

        $exception = new RecoverableCommandHandlerException('Recover', 0, [$command2]);

        $commandBus->dispatch([$command1, $command2])->willThrow($exception);
        $queue->enqueue($command2)->shouldBeCalled();

        $this->import([$command1, $command2]);
    }

    function it_does_not_republish_failed_unrecoverable_commands(CommandBus $commandBus, CommandQueueInterface $queue)
    {
        $command = new class implements CommandInterface {};

        $commandBus->dispatch([$command])->willThrow(CommandHandlerException::class);
        $queue->enqueue(Argument::any())->shouldNotBeCalled();

        $this->import([$command]);
    }

    function it_import_stops_for_all_other_exceptions(CommandBus $commandBus)
    {
        $command = new class implements CommandInterface {};

        $commandBus->dispatch([$command])->willThrow(\Exception::class);

        $this->shouldThrow(\Exception::class)->during('import', [[$command]]);
    }
}
