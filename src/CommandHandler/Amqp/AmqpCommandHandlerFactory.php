<?php

namespace Aa\AkeneoImport\CommandHandler\Amqp;

use Aa\AkeneoImport\Transport\TransportFactory;


class AmqpCommandHandlerFactory
{
    public function createByDsn(string $dsn): AmqpCommandHandler
    {
        $transportFactory = new TransportFactory($dsn);

        return new AmqpCommandHandler($transportFactory->createSender());
    }
}
