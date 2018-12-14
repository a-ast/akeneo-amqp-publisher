<?php

namespace Aa\AkeneoImport\MessageHandler;

use Aa\AkeneoImport\ImportCommands\CommandListHandlerInterface;
use Aa\AkeneoImport\ImportCommands\CommandListInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CommandListHandler implements MessageHandlerInterface
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
        // @todo: add logic for requeing / rejecting messages

        $this->handler->handle($commands);
    }
}
