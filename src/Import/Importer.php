<?php

namespace Aa\AkeneoImport\Import;

use Aa\AkeneoImport\CommandBus\CommandBusInterface;
use Aa\AkeneoImport\ImportCommand\CommandCallbacks;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Product\ProductFieldInterface;
use Aa\AkeneoImport\ImportCommand\ProductModel\ProductModelFieldInterface;
use Aa\AkeneoImport\Queue\CommandQueueInterface;
use Aa\AkeneoImport\Queue\InMemoryQueue;
use Psr\Log\LoggerAwareTrait;

class Importer implements ImporterInterface
{
    use LoggerAwareTrait;

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

    // Report
    private $totalCount = 0;
    private $totalRequeueCount = 0;
    private $tearDownCount = 0;

    public function __construct(CommandBusInterface $commandBus, int $maxRequeueCount = 4)
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

        $tailProcessed = false;

        do {
            $command = $queue->dequeue();

            if (null === $command) {
                $this->logger->info('***** End of queue. Tear down!');

                $this->commandBus->tearDown();
                $command = $queue->dequeue();

                if (null === $command) {
                    break;
                }
            }

            // tear down when processing republished commands
            if ($this->getRequeueCount($command) > 0 && false === $tailProcessed) {
                $this->logger->info('----- Tail. Tear down!');

                $this->commandBus->tearDown();

                $tailProcessed = true;
            }

            $this->dumpCommand($command);

            $this->commandBus->dispatch($command, $callbacks);

        } while (true); // exit only when queue is empty
    }

    private function getRequeueCount(CommandInterface $command): int
    {
        $commandUniqueId = $this->getCommandUniqueId($command);

        return $this->requeuedCommands[$commandUniqueId] ?? 0;
    }

    private function createCallBacks(CommandQueueInterface $queue): CommandCallbacks
    {
        return new CommandCallbacks(

            function (CommandInterface $command, string $message = '', int $code = 0, array $errors = []) use ($queue) {

                $requeueCount = $this->getRequeueCount($command);

                if ($requeueCount > $this->maxRequeueCount) {

                    throw new CommandHandlerException(
                        sprintf('%s (repeated: %d times)', $message, $requeueCount), $command, $code, $errors
                    );
                }

                $this->totalRequeueCount++;

                $this->requeuedCommands[$this->getCommandUniqueId($command)] = $requeueCount + 1;

                $queue->enqueue($command);
            }
        );
    }


    private function getCommandUniqueId(CommandInterface $command): string
    {

        // Debug version

        if ($command instanceof ProductModelFieldInterface) {
            return $command->getProductModelCode();
        }

        if ($command instanceof ProductFieldInterface) {
            return $command->getProductIdentifier();
        }

        return spl_object_hash($command);
    }

    private function dumpCommand(CommandInterface $command)
    {
        $this->logger->info($this->getCommandUniqueId($command));
    }
}
