<?php

namespace Aa\Akeneo\Import\Handler;

use Aa\AkeneoImport\ImportCommands\CommandInterface;
use Aa\AkeneoImport\ImportCommands\CommandHandlerInterface;
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
    private $commandDispenser = [];

    /**
     * @var int
     */
    private $maxCommandCount;

    public function __construct(MessageBusInterface $bus, int $maxCommandCount)
    {
        $this->bus = $bus;
        $this->maxCommandCount = $maxCommandCount;
    }

    public function __invoke(CommandInterface $command)
    {
        if ($command instanceof FinishImport) {
            $this->dispatchAll();

            return;
        }

        $commandClass = get_class($command);
        $this->commandDispenser[$commandClass][] = $command;

        if ($this->maxCommandCount <= count($this->commandDispenser[$commandClass])) {

            $commandList = new CommandList($this->commandDispenser[$commandClass]);

            $this->bus->dispatch($commandList);

            $this->commandDispenser[$commandClass] = [];
        }
    }

    protected function dispatchAll(): void
    {
        foreach ($this->commandDispenser as $commands) {

            if (!is_array($commands) || empty($commands)) {
                continue;
            }

            $commandList = new CommandList($commands);
            $this->bus->dispatch($commandList);
        }
    }
}
