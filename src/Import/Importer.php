<?php

namespace Aa\AkeneoImport\Import;

use Aa\AkeneoImport\CommandBus\CommandBusInterface;
use Aa\AkeneoImport\ImportCommand\CommandCallbacks;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\Queue\CommandQueueInterface;
use Aa\AkeneoImport\Queue\InMemoryQueue;

class Importer implements ImporterInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var int[]
     */
    private $requeuedCommands;

    /**
     * @var int
     */
    private $maxRequeueCount;

    public function __construct(CommandBusInterface $commandBus, int $maxRequeueCount = 2)
    {
        $this->commandBus = $commandBus;
        $this->maxRequeueCount = $maxRequeueCount;
    }

    public function import(iterable $commands)
    {
        $queue = new InMemoryQueue($commands);

        $this->importQueue($queue);
    }

    public function importQueue(CommandQueueInterface $queue)
    {
        $this->commandBus->setUp();

        $callbacks = $this->createCallBacks($queue);

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

    private function createCallBacks(CommandQueueInterface $queue): CommandCallbacks
    {
        return new CommandCallbacks(

            function (CommandInterface $command, string $message = '', int $code = 0, array $errors = []) use ($queue) {

                $requeueCount = $this->getRequeueCount($command);

                if ($requeueCount > $this->maxRequeueCount) {

                    throw new CommandHandlerException(
                        sprintf('%s (repeated: %d)', $message, $requeueCount), $command, $code, $errors
                    );
                }

                $this->requeuedCommands[spl_object_hash($command)] = $requeueCount + 1;

                $queue->enqueue($command);
            }
        );
    }
}
