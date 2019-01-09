<?php

namespace Aa\AkeneoImport\Transport;

use Aa\AkeneoImport\ImportCommand\CommandHandlerInterface;

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

    public function consume(CommandHandlerInterface $handler, string $queueName)
    {
        $receive = $this->receiver->receive($queueName);

        foreach ($receive as $commandBatch) {

            try {
                $handler->handle($commandBatch);
            } catch (\Exception $e) {
                $receive->throw($e);
            }
        }
    }
}
