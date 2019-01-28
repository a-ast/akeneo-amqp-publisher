<?php

namespace Aa\AkeneoImport\Import;

use Aa\AkeneoImport\CommandBus\CommandBus;
use Aa\AkeneoImport\CommandBus\CommandPromise;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Exception\RecoverableCommandHandlerException;
use Aa\AkeneoImport\Queue\CommandQueueInterface;
use Aa\AkeneoImport\Queue\InMemoryQueue;

class Importer implements ImporterInterface
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function import(iterable $commands)
    {
        $queue = new InMemoryQueue($commands);

        $this->importQueue($queue);
    }

    public function importQueue(CommandQueueInterface $queue)
    {
        $this->commandBus->setUp();

        do {
            $command = $queue->dequeue();

            if (null === $command) {

                $this->commandBus->tearDown();
                $command = $queue->dequeue();

                if (null === $command) {
                    break;
                }
            }

            $promise = new CommandPromise($command, function () use ($queue, $command) {

                $queue->enqueue($command);

            });

            $this->commandBus->dispatch($promise);

        } while (true); // exit only when queue is empty
    }
}
