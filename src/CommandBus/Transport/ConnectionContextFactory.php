<?php

namespace Aa\AkeneoImport\CommandBus\Transport;

use Enqueue\AmqpExt\AmqpConnectionFactory;

class ConnectionContextFactory
{
    /**
     * @var string
     */
    private $dsn;

    /**
     * @var array
     */
    private $options;

    public function __construct(string $dsn, array $options = [])
    {
        $this->dsn = $dsn;
        $this->options = $options;
    }

    public function createContext()
    {
        $factory = new AmqpConnectionFactory($this->dsn);

        $context = $factory->createContext();

        return $context;
    }
}
