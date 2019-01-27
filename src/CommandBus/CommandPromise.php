<?php

namespace Aa\AkeneoImport\CommandBus;

use Aa\AkeneoImport\ImportCommand\CommandInterface;

class CommandPromise
{
    /**
     * @var CommandInterface
     */
    private $command;

    /**
     * @var callable
     */
    private $repeatCallback;

    public function __construct(CommandInterface $command, callable $repeatCallback)
    {
        $this->command = $command;
        $this->repeatCallback = $repeatCallback;
    }

    public function repeat()
    {
        ($this->repeatCallback)();
    }

    public function getCommand(): CommandInterface
    {
        return $this->command;
    }
}
