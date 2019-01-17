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

    /**
     * @param CommandHandlerInterface[] $handlers
     */
    public function createCommandBus(iterable $handlers): MessageBusInterface
    {
        $messageHandlers = [];

        foreach ($handlers as $class => $handler) {
            $messageHandlers[$class] = [$class => new CommandMessageHandler($handler)];
        }

        $handlersLocator = new HandlersLocator($messageHandlers);

        $middlewares[] = new HandleMessageMiddleware($handlersLocator);

        $commandBus = new MessageBus($middlewares);

        return $commandBus;
    }
}
