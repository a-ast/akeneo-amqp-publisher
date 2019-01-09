<?php

namespace Aa\AkeneoImport\MessageHandler;

use Aa\AkeneoImport\ImportCommand\CommandHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandBatchInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CommandBatchMessageHandler implements MessageHandlerInterface
{
    /**
     * @var CommandHandlerInterface
     */
    private $handler;

    public function __construct(CommandHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    public function __invoke(CommandBatchInterface $commands)
    {
        $this->handler->handle($commands);
    }
}
