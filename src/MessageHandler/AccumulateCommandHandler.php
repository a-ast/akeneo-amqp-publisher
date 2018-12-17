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

    public function __construct(int $maxCommandCount)
    {
        $this->maxCommandCount = $maxCommandCount;
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

        if ($this->maxCommandCount <= count($this->commandAccumulator[$commandClass])) {

            $commandList = new CommandList($this->commandAccumulator[$commandClass]);

            $this->bus->dispatch($commandList);

            $this->commandAccumulator[$commandClass] = [];
        }
    }

    protected function dispatchAll(): void
    {
        foreach ($this->commandAccumulator as $commands) {

            if (!is_array($commands) || empty($commands)) {
                continue;
            }

            $commandList = new CommandList($commands);
            $this->bus->dispatch($commandList);
        }
    }
}
