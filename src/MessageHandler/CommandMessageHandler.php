<?php

namespace Aa\AkeneoImport\MessageHandler;

use Aa\AkeneoImport\ImportCommand\CommandHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CommandMessageHandler implements MessageHandlerInterface
{
    /**
     * @var CommandHandlerInterface
     */
    private $handler;

    public function __construct(CommandHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    public function __invoke(CommandInterface $command)
    {
        $this->handler->handle($command);
    }
}
