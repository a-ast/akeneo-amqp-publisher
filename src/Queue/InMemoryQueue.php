<?php

namespace Aa\AkeneoImport\Queue;

use Aa\AkeneoImport\ImportCommand\CommandInterface;

class InMemoryQueue implements CommandQueueInterface
{
    /**
     * @var \SplQueue
     */
    private $queue;

    public function __construct()
    {
        $this->queue = new \SplQueue();
    }

    public function enqueue(CommandInterface $command)
    {
        $this->queue->enqueue($command);
    }

    public function dequeue(): ?CommandInterface
    {
        try {
            return $this->queue->dequeue();
        } catch (\RuntimeException $e) {
            return null;
        }
    }
}
