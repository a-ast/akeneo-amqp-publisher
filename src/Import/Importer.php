<?php

namespace Aa\AkeneoImport\Import;

use Aa\AkeneoImport\CommandBus\CommandBusFactory;
use Aa\AkeneoImport\ImportCommands\CommandListHandlerInterface;
use Aa\AkeneoImport\ImportCommands\CommandProviderInterface;
use Aa\AkeneoImport\ImportCommands\Control\FinishImport;


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

    public function import(CommandProviderInterface $provider, CommandListHandlerInterface $handler)
    {
        $bus = $this->commandBusFactory->createCommandBus($handler);

        foreach ($provider->getCommands() as $command) {
            $bus->dispatch($command);
        }

        $bus->dispatch(new FinishImport());
    }
}
