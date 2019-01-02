<?php

namespace Aa\AkeneoImport\MessageHandler;

use Aa\AkeneoImport\Exception\RejectMessageException;
use Aa\AkeneoImport\ImportCommand\CommandListHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandListInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Exception\RecoverableCommandHandlerException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Throwable;

class CommandListMessageHandler implements MessageHandlerInterface
{
    /**
     * @var CommandListHandlerInterface
     */
    private $handler;

    public function __construct(CommandListHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    public function __invoke(CommandListInterface $commands)
    {
        $this->handler->handle($commands);
    }
}
