<?php

namespace Aa\AkeneoImport\CommandBus;

use Aa\AkeneoImport\Transport\Receiver;
use Aa\AkeneoImport\ImportCommand\CommandBatchHandlerInterface;

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

    public function consume(CommandBatchHandlerInterface $handler, string $queueName)
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
