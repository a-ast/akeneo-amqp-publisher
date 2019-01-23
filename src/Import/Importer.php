<?php

namespace Aa\AkeneoImport\Import;

use Aa\AkeneoImport\ImportCommand\CommandHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\InitializableCommandHandlerInterface;

class Importer
{
    /**
     * @var iterable|CommandHandlerInterface[]
     */
    private $handlers;

    public function __construct(iterable $handlers)
    {
        $this->handlers = $handlers;
    }

    public function import(iterable $commands)
    {
        $this->setUpHandlers();

        foreach ($commands as $command) {

            $handler = $this->findHandlerFor($command);
            $handler->handle($command);
        }

        $this->tearDownHandlers();
    }

    private function getCommandTypes(CommandInterface $command)
    {
        $class = get_class($command);

        return [$class] + array_values(class_parents($class)) + array_values(class_implements($class));
    }

    private function setUpHandlers(): void
    {
        foreach ($this->handlers as $handler) {
            if ($handler instanceof InitializableCommandHandlerInterface) {
                $handler->setUp();
            }
        }
    }

    private function tearDownHandlers(): void
    {
        foreach ($this->handlers as $handler) {
            if ($handler instanceof InitializableCommandHandlerInterface) {
                $handler->tearDown();
            }
        }
    }

    private function findHandlerFor(CommandInterface $command): CommandHandlerInterface
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
