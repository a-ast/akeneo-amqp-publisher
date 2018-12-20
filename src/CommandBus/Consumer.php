<?php

namespace Aa\AkeneoImport\CommandBus;

use Aa\AkeneoImport\CommandBus\Transport\Receiver;
use Aa\AkeneoImport\ImportCommands\CommandListHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Component\Messenger\Worker;

class Consumer
{
    /**
     * @var Receiver
     */
    private $receiver;

    /**
     * @var CommandBusFactory
     */
    private $busFactory;

    public function __construct(Receiver $receiver, CommandBusFactory $busFactory)
    {
        $this->receiver = $receiver;
        $this->busFactory = $busFactory;
    }

    public function consume(CommandListHandlerInterface $handler)
    {
        $bus = $this->busFactory->createCommandBus($handler);

        foreach ($this->receiver as $commandList) {

            $bus->dispatch($commandList);

        }


    }
}
