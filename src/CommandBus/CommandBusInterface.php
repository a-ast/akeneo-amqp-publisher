<?php

namespace Aa\AkeneoImport\CommandBus;

use Aa\AkeneoImport\ImportCommand\CommandCallbacks;
use Aa\AkeneoImport\ImportCommand\CommandInterface;

interface CommandBusInterface
{
    public function dispatch(CommandInterface $command, CommandCallbacks $callbacks = null);

    public function setUp(): void;

    public function tearDown(): void;
}
