<?php

namespace Aa\AkeneoImport\CommandHandler\Amqp;

use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\Transport\Sender;
use Aa\AkeneoImport\ImportCommand\CommandHandlerInterface;


class AmqpCommandHandler implements CommandHandlerInterface
{
    /**
     * @var Sender
     */
    private $sender;

    public function __construct(Sender $sender)
    {
        $this->sender = $sender;
    }

    public function handle(CommandInterface $command)
    {
        $this->sender->send($command);
    }
}
