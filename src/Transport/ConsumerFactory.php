<?php

namespace Aa\AkeneoImport\Transport;

class ConsumerFactory
{
    public function createByDsn(string $dsn): Consumer
    {
        $factory = new TransportFactory($dsn);

        return new Consumer($factory->createReceiver());
    }
}
