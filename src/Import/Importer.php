<?php

namespace Aa\AkeneoImport\Import;

use Aa\AkeneoImport\CommandBus\CommandBus;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Exception\RecoverableCommandHandlerException;
use Aa\AkeneoImport\Queue\CommandQueueInterface;

class Importer implements ImporterInterface
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var \Aa\AkeneoImport\Queue\CommandQueueInterface
     */
    private $queue;

    public function __construct(CommandBus $commandBus, CommandQueueInterface $queue)
    {
        $this->commandBus = $commandBus;
        $this->queue = $queue;
    }

    public function import(iterable $commands)
    {
        foreach ($commands as $command) {
            $this->queue->enqueue($command);
        }

        do {
            $command = $this->queue->dequeue();

            if (null === $command) {
                break;
            }

            try {

                $this->commandBus->dispatch($command);

            } catch (RecoverableCommandHandlerException $e) {

                $this->publishFailedCommands($e->getCommands());

            } catch (CommandHandlerException $e) {

                // do nothing
            }

        } while ($command !== null);
    }

    private function publishFailedCommands(iterable $commands): void
    {
        foreach ($commands as $failedCommand) {
            $this->queue->enqueue($failedCommand);
        }
    }
}
