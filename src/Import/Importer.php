<?php

namespace Aa\AkeneoImport\Import;

use Aa\AkeneoImport\CommandBus\CommandBusFactory;
use Aa\AkeneoImport\ImportCommand\CommandHandlerInterface;
use Aa\AkeneoImport\ImportCommand\Control\FinishImport;


class Importer
{
    /**
     * @var \Aa\AkeneoImport\CommandBus\CommandBusFactory
     */
    private $commandBusFactory;

    public function __construct(CommandBusFactory $commandBusFactory)
    {
        $this->commandBusFactory = $commandBusFactory;
    }

    public function import(iterable $commands, CommandHandlerInterface $handler)
    {
        // @todo: generate import id and send with envelope

        $bus = $this->commandBusFactory->createCommandBus($handler);

        foreach ($commands as $command) {
            $bus->dispatch($command);
        }

        $bus->dispatch(new FinishImport());
    }
}
