<?php

namespace Aa\AkeneoImport\Import;

use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Exception\RecoverableCommandHandlerException;
use Aa\AkeneoImport\Queue\CommandQueueInterface;

class RepublishingImporter
{

    /**
     * @var \Aa\AkeneoImport\Import\ImporterInterface
     */
    private $importer;

    /**
     * @var \Aa\AkeneoImport\Queue\CommandQueueInterface
     */
    private $queue;

    public function __construct(ImporterInterface $importer, CommandQueueInterface $queue)
    {
        $this->importer = $importer;
        $this->queue = $queue;
    }

    public function import(iterable $commands)
    {
        try {
            $this->importer->import($commands);
        } catch (RecoverableCommandHandlerException $e) {

            foreach ($e->getCommands() as $command) {
                $this->queue->enqueue($command);
            }
        } catch (CommandHandlerException $e) {
            // do nothing
        }
    }
}
