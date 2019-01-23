<?php

namespace Aa\AkeneoImport\Queue;

use Aa\AkeneoImport\ImportCommand\CommandInterface;

interface CommandQueueInterface
{
    public function enqueue(CommandInterface $command);

    public function dequeue(): ?CommandInterface;
}
