<?php

namespace Aa\AkeneoImport\CommandBus;

use Aa\AkeneoImport\ImportCommand\AsyncCommandHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\InitializableCommandHandlerInterface;

class CommandBus
{
    /**
     * @var CommandHandlerInterface[]
     */
    private $handlers;

    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
    }

    public function dispatch(CommandPromise $commandPromise)
    {
        $handler = $this->findHandlerFor($commandPromise->getCommand());

        if ($handler instanceof AsyncCommandHandlerInterface) {
            return $handler->handle($commandPromise);
        }


        return $handler->handle($commandPromise->getCommand());
    }

    private function getCommandTypes(CommandInterface $command): array
    {
        $class = get_class($command);

        return [$class] + array_values(class_parents($class)) + array_values(class_implements($class));
    }

    public function setUp(): void
    {
        foreach ($this->handlers as $handler) {
            if ($handler instanceof InitializableCommandHandlerInterface) {
                $handler->setUp();
            }
        }
    }

    public function tearDown(): void
    {
        foreach ($this->handlers as $handler) {
            if ($handler instanceof InitializableCommandHandlerInterface) {
                $handler->tearDown();
            }
        }
    }

    private function findHandlerFor(CommandInterface $command) //: CommandHandlerInterface
    {
        $commandTypes = $this->getCommandTypes($command);
        $availableTypes = array_keys($this->handlers);

        $supportedTypes = array_intersect($commandTypes, $availableTypes);

        $firstSupportedType = array_shift($supportedTypes);

        if (null === $firstSupportedType) {
            throw new CommandHandlerException(sprintf('No handler found for the command %s', get_class($command)));
        }

        return $this->handlers[$firstSupportedType];
    }
}
