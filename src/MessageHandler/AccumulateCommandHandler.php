<?php

namespace Aa\AkeneoImport\MessageHandler;

use Aa\AkeneoImport\ImportCommands\CommandInterface;
use Aa\AkeneoImport\ImportCommands\CommandsHandlerInterface;
use Aa\AkeneoImport\ImportCommands\CommandList;
use Aa\AkeneoImport\ImportCommands\Control\FinishImport;
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

        $commandClass = get_class($command);
        $this->commandAccumulator[$commandClass][] = $command;

        if ($this->maxCommandCount > count($this->commandAccumulator[$commandClass])) {
            return;
        }

        (true === $this->shouldKeepCommandOrder) ?
            $this->dispatchAll() :
            $this->dispatchCommandList($commandClass);
    }

    protected function dispatchAll()
    {
        foreach (array_keys($this->commandAccumulator) as $commandClass) {
            $this->dispatchCommandList($commandClass);
        }
    }

    protected function dispatchCommandList(string $commandClass)
    {
        if (!isset($this->commandAccumulator[$commandClass]) ||
            !is_array($this->commandAccumulator[$commandClass]) ||
            empty($this->commandAccumulator[$commandClass])) {

            return;
        }

        $commandList = new CommandList($this->commandAccumulator[$commandClass]);
        $this->bus->dispatch($commandList);

        $this->commandAccumulator[$commandClass] = [];
    }
}
