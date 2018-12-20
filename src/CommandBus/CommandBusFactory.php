<?php

namespace Aa\AkeneoImport\CommandBus;

use Aa\AkeneoImport\ImportCommands\CommandInterface;
use Aa\AkeneoImport\ImportCommands\CommandListHandlerInterface;
use Aa\AkeneoImport\ImportCommands\CommandListInterface;
use Aa\AkeneoImport\MessageHandler\AccumulateCommandHandler;
use Aa\AkeneoImport\MessageHandler\CommandListMessageHandler;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

class CommandBusFactory
{
    public function createCommandBus(CommandListHandlerInterface $handler): MessageBusInterface
    {
        $commandListHandler = new CommandListMessageHandler($handler);
        $commandHandler = new AccumulateCommandHandler( 100, $handler->shouldKeepCommandOrder());

        $handlersLocator = new HandlersLocator([
            CommandListInterface::class => [ CommandListInterface::class => $commandListHandler],
            CommandInterface::class => [ CommandInterface::class => $commandHandler],
        ]);

        $middlewares[] = new HandleMessageMiddleware($handlersLocator);

        $commandBus = new MessageBus($middlewares);

        $commandHandler->setMessageBus($commandBus);

        return $commandBus;
    }
}
