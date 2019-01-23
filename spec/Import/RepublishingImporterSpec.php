<?php

namespace spec\Aa\AkeneoImport\Import;

use Aa\AkeneoImport\Import\ImporterInterface;
use Aa\AkeneoImport\Import\RepublishingImporter;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Exception\RecoverableCommandHandlerException;
use Aa\AkeneoImport\Queue\CommandQueueInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RepublishingImporterSpec extends ObjectBehavior
{
    function let(ImporterInterface $importer, CommandQueueInterface $queue)
    {
        $this->beConstructedWith($importer, $queue);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RepublishingImporter::class);
    }

    function it_republishes_failed_recoverable_commands(ImporterInterface $importer, CommandQueueInterface $queue)
    {
        $command1 = new class implements CommandInterface {};
        $command2 = new class implements CommandInterface {};

        $exception = new RecoverableCommandHandlerException('Recover', 0, [$command2]);

        $importer->import([$command1, $command2])->willThrow($exception);
        $queue->enqueue($command2)->shouldBeCalled();

        $this->import([$command1, $command2]);
    }

    function it_does_not_republish_failed_unrecoverable_commands(ImporterInterface $importer, CommandQueueInterface $queue)
    {
        $command = new class implements CommandInterface {};

        $importer->import([$command])->willThrow(CommandHandlerException::class);
        $queue->enqueue(Argument::any())->shouldNotBeCalled();

        $this->import([$command]);
    }

    function it_import_stops_for_all_other_exceptions(ImporterInterface $importer, CommandQueueInterface $queue)
    {
        $command = new class implements CommandInterface {};

        $importer->import([$command])->willThrow(\Exception::class);

        $this->shouldThrow(\Exception::class)->during('import', [[$command]]);
    }
}
