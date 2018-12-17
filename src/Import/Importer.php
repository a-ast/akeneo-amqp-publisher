<?php

namespace Aa\AkeneoImport\Import;

use Aa\AkeneoImport\CommandBus\CommandBusFactory;
use Aa\AkeneoImport\ImportCommands\CommandListHandlerInterface;
use Aa\AkeneoImport\ImportCommands\CommandProviderInterface;
use Aa\AkeneoImport\ImportCommands\Control\FinishImport;


class Importer
{
    /**
     * @var CommandProviderInterface
     */
    private $provider;

    /**
     * @var \Aa\AkeneoImport\CommandBus\CommandBusFactory
     */
    private $commandBusFactory;

    public function __construct(CommandProviderInterface $provider, CommandBusFactory $commandBusFactory)
    {
        $this->provider = $provider;
        $this->commandBusFactory = $commandBusFactory;
    }

    public function import(CommandListHandlerInterface $handler)
    {
        $bus = $this->commandBusFactory->createCommandBus($handler);

        foreach ($this->provider->getCommands() as $command) {
            $bus->dispatch($command);
        }

        $bus->dispatch(new FinishImport());
    }
}
