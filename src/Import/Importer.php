<?php

namespace Aa\AkeneoImport\Import;

use Aa\AkeneoImport\ImportCommands\CommandProviderInterface;
use Aa\AkeneoImport\ImportCommands\Control\FinishImport;
use Symfony\Component\Messenger\MessageBusInterface;

class Importer
{
    /**
     * @var CommandProviderInterface
     */
    private $provider;

    /**
     * @var MessageBusInterface
     */
    private $bus;

    public function __construct(CommandProviderInterface $provider, MessageBusInterface $bus)
    {
        $this->provider = $provider;
        $this->bus = $bus;
    }

    public function import()
    {
        foreach ($this->provider->getCommands() as $command) {
            $this->bus->dispatch($command);
        }

        $this->bus->dispatch(new FinishImport());
    }
}
