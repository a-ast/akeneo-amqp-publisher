<?php

namespace Aa\AkeneoImport\CommandHandler\Api\Handler;

use Aa\AkeneoImport\ImportCommand\CommandInterface;

class CommandAccumulator
{
    /**
     * @var int
     */
    private $maxBatchSize;

    /**
     * @var array|CommandInterface[]
     */
    private $commands = [];

    /**
     * @var array|string[]
     */
    private $ids = [];

    public function __construct(int $maxBatchSize)
    {
        $this->maxBatchSize = $maxBatchSize;
    }

    public function add(string $id, CommandInterface $command)
    {
        $this->commands[] = $command;
        $this->ids = $this->getUniqueIds($id);
    }

    public function isFullAfter(string $id): bool
    {
        return count($this->getUniqueIds($id)) > $this->maxBatchSize;
    }

    public function getCommands(): iterable
    {
        return $this->commands;
    }

    public function clear(): void
    {
        $this->commands = [];
        $this->ids = [];
    }

    private function getUniqueIds(string $id): array
    {
        return array_unique(array_merge($this->ids, [$id]));
    }
}
