<?php

namespace Aa\AkeneoImport\Import;

use Aa\AkeneoImport\Queue\CommandQueueInterface;

interface ImporterInterface
{
    public function import(iterable $commands);

    public function importQueue(CommandQueueInterface $queue);
}
