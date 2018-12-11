<?php

namespace Aa\Akeneo\Import\Handler;

use Aa\Akeneo\ImportCommands\CommandHandlerInterface;
use Aa\Akeneo\ImportCommands\CommandListInterface;
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
        $this->handler->handle($commands);
    }
}
