<?php

namespace Aa\Akeneo\Import\Handler;

use Aa\AkeneoImport\ImportCommands\CommandHandlerInterface;
use Aa\AkeneoImport\ImportCommands\CommandListInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CommandListHandler implements MessageHandlerInterface
{
    /**
     * @var \Aa\Akeneo\ImportCommands\CommandHandlerInterface
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
