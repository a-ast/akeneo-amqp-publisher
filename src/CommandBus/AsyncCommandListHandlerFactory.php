<?php

namespace Aa\AkeneoImport\CommandBus;

use Aa\AkeneoImport\CommandHandler\AsyncCommandListHandler;
use Aa\AkeneoImport\ImportCommands\CommandListHandlerInterface;
use Aa\AkeneoImport\Serializer\CommandListNormalizer;
use Aa\AkeneoImport\Serializer\CommandNormalizer;
use Symfony\Component\Messenger\Transport\AmqpExt\AmqpSender;
use Symfony\Component\Messenger\Transport\AmqpExt\Connection;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;
use Symfony\Component\Messenger\Transport\Serialization\Serializer as MessengerSerializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateIntervalNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;

class AsyncCommandListHandlerFactory
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

    public function createHandler(): CommandListHandlerInterface
    {
        return new AsyncCommandListHandler($this->createSender());
    }

    protected function createSender(): SenderInterface
    {
        $connection = Connection::fromDsn($this->dsn, $this->options);

        $symfonySerializer = $this->createSerializer();

        $messengerSerializer = new MessengerSerializer($symfonySerializer);

        $sender = new AmqpSender($connection, $messengerSerializer);

        return $sender;
    }

    /**
     * @return \Symfony\Component\Serializer\Serializer
     */
    protected function createSerializer(): SymfonySerializer
    {
        $normalizers = [
            new CommandListNormalizer(),
            new CommandNormalizer(),
            new JsonSerializableNormalizer(),
            new DateTimeNormalizer(),
            new DateIntervalNormalizer(),
            new ArrayDenormalizer(),
            new ObjectNormalizer(),
        ];

        $encoders = [
            new JsonEncoder(),
        ];

        $symfonySerializer = new SymfonySerializer($normalizers, $encoders);

        return $symfonySerializer;
    }
}
