<?php

namespace Aa\AkeneoImport\Import;

use Aa\AkeneoImport\CommandBus\CommandBus;
use Aa\AkeneoImport\ImportCommand\CommandCallbacks;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\Queue\CommandQueueInterface;
use Aa\AkeneoImport\Queue\InMemoryQueue;

class Importer implements ImporterInterface
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var int[]
     */
    private $requeuedCommands;

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

            $requeueCount = $this->getRequeueCount($command);

            if ($requeueCount > 2) {

                // @todo: extend repeat callback with message to know exact reason
                throw new CommandHandlerException('Command can\'t be processed', $command);
            }
            $this->requeuedCommands[spl_object_hash($command)] = $requeueCount + 1;

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

    private function getRequeueCount(CommandInterface $command): int
    {
        return $this->requeuedCommands[spl_object_hash($command)] ?? 0;
    }
}
