<?php

namespace Aa\AkeneoImport\CommandBus;

use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\CommandHandlerInterface;
use Aa\AkeneoImport\ImportCommand\Product\DeleteProduct;
use Aa\AkeneoImport\MessageHandler\AccumulateCommandHandler;
use Aa\AkeneoImport\MessageHandler\CommandMessageHandler;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

class CommandBusFactory
{
    public function createCommandBus(CommandHandlerInterface $handler): MessageBusInterface
    {
//        $commandHandler = new AccumulateCommandHandler( 100, $handler->shouldKeepCommandOrder());




        $handlersLocator = new HandlersLocator([
            DeleteProduct::class => [ DeleteProduct::class => $deleteHandler],
            CommandInterface::class => [ CommandInterface::class => $commandHandler],
        ]);

        $middlewares[] = new HandleMessageMiddleware($handlersLocator);

        $commandBus = new MessageBus($middlewares);

        $commandHandler->setMessageBus($commandBus);

        return $commandBus;
    }
}
