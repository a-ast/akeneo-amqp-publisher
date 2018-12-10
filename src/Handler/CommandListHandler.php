<?php

namespace Aa\Akeneo\Import\Handler;

use Aa\Akeneo\ImportCommands\CommandCollectionInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CommandListHandler implements MessageHandlerInterface
{
    public function __invoke(CommandCollectionInterface $commands)
    {
        var_dump($commands);
    }
}
