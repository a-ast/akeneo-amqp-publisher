<?php

namespace Aa\AkeneoImport\CommandHandler;

use Aa\AkeneoImport\ImportCommands\CommandListHandlerInterface;
use Aa\AkeneoImport\ImportCommands\CommandListInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;

class AsyncCommandListHandler implements CommandListHandlerInterface
{
    /**
     * @var \Symfony\Component\Messenger\Transport\Sender\SenderInterface
     */
    private $sender;

    public function __construct(SenderInterface $sender)
    {
        $this->sender = $sender;
    }

    public function handle(CommandListInterface $commands)
    {
        $envelope = new Envelope($commands);

        $this->sender->send($envelope);
    }

    public function shouldKeepCommandOrder(): bool
    {
        return false;
    }
}
