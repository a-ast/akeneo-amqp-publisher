<?php

namespace Aa\AkeneoImport\CommandBus;

use Aa\AkeneoImport\CommandBus\Transport\Receiver;
use Aa\AkeneoImport\ImportCommand\CommandListHandlerInterface;

class Consumer
{
    /**
     * @var Receiver
     */
    private $receiver;

    public function __construct(Receiver $receiver)
    {
        $this->receiver = $receiver;
    }

    public function consume(CommandListHandlerInterface $handler, string $queueName)
    {
        $receive = $this->receiver->receive($queueName);

        foreach ($receive as $commandList) {

            try {
                $handler->handle($commandList);
            } catch (\Exception $e) {
                $receive->throw($e);
            }
        }
    }
}
