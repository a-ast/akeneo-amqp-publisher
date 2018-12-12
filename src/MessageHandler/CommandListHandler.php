<?php

namespace Aa\AkeneoImport\MessageHandler;

use Aa\AkeneoImport\ImportCommands\CommandHandlerInterface;
use Aa\AkeneoImport\ImportCommands\CommandListInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CommandListHandler implements MessageHandlerInterface
{
    /**
     * @var CommandHandlerInterface
     */
    private $handler;

    public function __construct(CommandHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    public function __invoke(CommandListInterface $commands)
    {
        // @todo: add logic for requeing / rejecting messages

        sprintf('Got %d commands', count($commands));

        $this->handler->handle($commands);
    }
}
