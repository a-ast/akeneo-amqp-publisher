<?php

namespace Aa\AkeneoImport\MessageHandler;

use Aa\AkeneoImport\ImportCommand\CommandBatchHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandBatchInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CommandBatchMessageHandler implements MessageHandlerInterface
{
    /**
     * @var CommandBatchHandlerInterface
     */
    private $handler;

    public function __construct(CommandBatchHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    public function __invoke(CommandBatchInterface $commands)
    {
        $this->handler->handle($commands);
    }
}
