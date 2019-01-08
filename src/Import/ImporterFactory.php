<?php

namespace Aa\AkeneoImport\Import;

use Aa\AkeneoImport\CommandBus\CommandBusFactory;

class ImporterFactory
{
    public function create(): Importer
    {
        return new Importer(new CommandBusFactory());
    }
}
