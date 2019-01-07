<?php

namespace Aa\AkeneoImport\CommandBus;

use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\CommandBatchHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandBatchInterface;
use Aa\AkeneoImport\MessageHandler\AccumulateCommandHandler;
use Aa\AkeneoImport\MessageHandler\CommandBatchMessageHandler;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

class CommandBusFactory
{
    public function createCommandBus(CommandBatchHandlerInterface $handler): MessageBusInterface
    {
        $commandBatchHandler = new CommandBatchMessageHandler($handler);
        $commandHandler = new AccumulateCommandHandler( 100, $handler->shouldKeepCommandOrder());

        $handlersLocator = new HandlersLocator([
            CommandBatchInterface::class => [ CommandBatchInterface::class => $commandBatchHandler],
            CommandInterface::class => [ CommandInterface::class => $commandHandler],
        ]);

        $middlewares[] = new HandleMessageMiddleware($handlersLocator);

        $commandBus = new MessageBus($middlewares);

        $commandHandler->setMessageBus($commandBus);

        return $commandBus;
    }
}
