<?php

namespace Aa\AkeneoImport\Import;

use Aa\AkeneoImport\CommandBus\CommandBus;
use Aa\AkeneoImport\ImportCommand\CommandCallbacks;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
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

        $callbacks = new CommandCallbacks(function (CommandInterface $command) use ($queue) {

            $queue->enqueue($command);

        });

        do {
            $command = $queue->dequeue();

            if (null === $command) {

                $this->commandBus->tearDown();
                $command = $queue->dequeue();

                if (null === $command) {
                    break;
                }
            }

            $this->commandBus->dispatch($command, $callbacks);

        } while (true); // exit only when queue is empty
    }
}
