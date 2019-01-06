<?php

namespace Aa\AkeneoImport\MessageHandler;

use Aa\AkeneoImport\Exception\RejectMessageException;
use Aa\AkeneoImport\ImportCommand\CommandBatchHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandBatchInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Exception\RecoverableCommandHandlerException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Throwable;

class CommandListMessageHandler implements MessageHandlerInterface
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
