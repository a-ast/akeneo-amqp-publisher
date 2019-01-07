<?php

namespace Aa\AkeneoImport\MessageHandler;

use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\CommandsAwareInterface;
use Aa\AkeneoImport\ImportCommand\CommandBatch;
use Aa\AkeneoImport\ImportCommand\Control\FinishImport;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @internal
 */
class AccumulateCommandHandler implements MessageHandlerInterface
{
    /**
     * @var MessageBusInterface
     */
    private $bus;

    /**
     * @var array
     */
    private $commandAccumulator = [];

    /**
     * @var int
     */
    private $maxCommandCount;

    /**
     * @var bool
     */
    private $shouldKeepCommandOrder;

    public function __construct(int $maxCommandCount, bool $shouldKeepCommandOrder)
    {
        $this->maxCommandCount = $maxCommandCount;
        $this->shouldKeepCommandOrder = $shouldKeepCommandOrder;
    }

    public function setMessageBus(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function __invoke(CommandInterface $command)
    {
        if ($command instanceof FinishImport) {
            $this->dispatchAll();

            return;
        }

        $this->addCommand($command);

        if ($command instanceof CommandsAwareInterface) {
            foreach ($command->getExtraCommands() as $extraCommand) {
                $this->addCommand($extraCommand);
            }
        }
    }

    private function addCommand(CommandInterface $command)
    {
        $commandClass = get_class($command);
        $this->commandAccumulator[$commandClass][] = $command;

        $this->dispatchIfLimitReached();
    }

    private function dispatchIfLimitReached()
    {
        foreach ($this->commandAccumulator as $commandClass => $commands) {

            if (count($commands) < $this->maxCommandCount) {
                continue;
            }

            (true === $this->shouldKeepCommandOrder) ?
                $this->dispatchAll() :
                $this->dispatchCommandBatch($commandClass);

            return;
        }
    }

    private function dispatchAll()
    {
        foreach (array_keys($this->commandAccumulator) as $commandClass) {
            $this->dispatchCommandBatch($commandClass);
        }
    }

    private function dispatchCommandBatch(string $commandClass)
    {
        if (!isset($this->commandAccumulator[$commandClass]) ||
            !is_array($this->commandAccumulator[$commandClass]) ||
            empty($this->commandAccumulator[$commandClass])) {

            return;
        }

        $commandBatch = new CommandBatch($this->commandAccumulator[$commandClass]);
        $this->bus->dispatch($commandBatch);

        $this->commandAccumulator[$commandClass] = [];
    }
}
