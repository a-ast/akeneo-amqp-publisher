<?php

namespace Aa\Akeneo\Import\Handler;

use Aa\Akeneo\ImportCommands\CommandListInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CommandListHandler implements MessageHandlerInterface
{
    public function __invoke(CommandListInterface $commands)
    {
        var_dump($commands);
    }
}
