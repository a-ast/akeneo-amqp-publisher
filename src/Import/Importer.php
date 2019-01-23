<?php

namespace Aa\AkeneoImport\Import;

use Aa\AkeneoImport\CommandBus\CommandBus;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Exception\RecoverableCommandHandlerException;
use Aa\AkeneoImport\Queue\CommandQueueInterface;

class Importer implements ImporterInterface
{

    /**
     * @var \Aa\AkeneoImport\Import\ImporterInterface
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
        try {
            $this->commandBus->dispatch($commands);
        } catch (RecoverableCommandHandlerException $e) {

            foreach ($e->getCommands() as $command) {
                $this->queue->enqueue($command);
            }
        } catch (CommandHandlerException $e) {
            // do nothing
        }
    }
}
