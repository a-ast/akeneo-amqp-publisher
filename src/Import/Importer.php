<?php

namespace Aa\AkeneoImport\Import;

use Aa\AkeneoImport\CommandBus\CommandBus;
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
        $queue = $this->getQueue($commands);

        $this->commandBus->setUp();

        do {
            $command = $queue->dequeue();

            if (null === $command) {
                break;
            }

            try {

                $this->commandBus->dispatch($command);

            } catch (RecoverableCommandHandlerException $e) {

                $this->publishFailedCommands($queue, $e->getCommands());

            } catch (CommandHandlerException $e) {

                // do nothing
            }

        } while ($command !== null);

        $this->commandBus->tearDown();
    }

    private function publishFailedCommands(CommandQueueInterface $queue, iterable $commands): void
    {
        foreach ($commands as $failedCommand) {
            $queue->enqueue($failedCommand);
        }
    }

    private function getQueue(iterable $commands): CommandQueueInterface
    {
        if (!$commands instanceof CommandQueueInterface) {
            return new InMemoryQueue($commands);
        }

        return $commands;
    }
}
