<?php

namespace Aa\Akeneo\Import\Handler;

use Aa\Akeneo\ImportCommands\CommandInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CommandHandler implements MessageHandlerInterface
{
    public function __invoke(CommandInterface $command)
    {
        var_dump($command);
    }
}
