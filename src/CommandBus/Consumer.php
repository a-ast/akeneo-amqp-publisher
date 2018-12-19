<?php

namespace Aa\AkeneoImport\CommandBus;

use Aa\AkeneoImport\ImportCommands\CommandListHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Component\Messenger\Worker;

class Consumer
{
    /**
     * @var \Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface
     */
    private $receiver;

    /**
     * @var \Aa\AkeneoImport\CommandBus\CommandBusFactory
     */
    private $busFactory;

    public function __construct(ReceiverInterface $receiver, CommandBusFactory $busFactory)
    {
        $this->receiver = $receiver;
        $this->busFactory = $busFactory;
    }

    public function consume(CommandListHandlerInterface $handler)
    {
        $bus = $this->busFactory->createCommandBus($handler);

        $worker = new Worker($this->receiver, $bus);
        try {
            $worker->run();
        } catch (\Exception $e) {

            var_dump($e);

        }
    }
}
