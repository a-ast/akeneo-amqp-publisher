<?php

namespace Aa\AkeneoImport\CommandHandler\Amqp;

use Aa\AkeneoImport\CommandBus\Transport\Sender;
use Aa\AkeneoImport\ImportCommand\CommandBatchHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandBatchInterface;


class AsyncCommandBatchHandler implements CommandBatchHandlerInterface
{
    /**
     * @var Sender
     */
    private $sender;

    public function __construct(Sender $sender)
    {
        $this->sender = $sender;
    }

    public function handle(CommandBatchInterface $commands)
    {
        $this->sender->send($commands);
    }

    public function shouldKeepCommandOrder(): bool
    {
        return false;
    }
}
