<?php

namespace Aa\AkeneoImport\CommandHandler;

use Aa\AkeneoImport\CommandBus\Transport\Sender;
use Aa\AkeneoImport\ImportCommands\CommandListHandlerInterface;
use Aa\AkeneoImport\ImportCommands\CommandListInterface;


class AsyncCommandListHandler implements CommandListHandlerInterface
{
    /**
     * @var Sender
     */
    private $sender;

    public function __construct(Sender $sender)
    {
        $this->sender = $sender;
    }

    public function handle(CommandListInterface $commands)
    {
        $this->sender->send($commands);
    }

    public function shouldKeepCommandOrder(): bool
    {
        return false;
    }
}
