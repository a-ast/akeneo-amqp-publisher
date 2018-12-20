<?php

namespace Aa\AkeneoImport\CommandBus;

use Aa\AkeneoImport\Serializer\CommandListNormalizer;
use Aa\AkeneoImport\Serializer\CommandNormalizer;
use Symfony\Component\Messenger\Transport\AmqpExt\AmqpSender;
use Symfony\Component\Messenger\Transport\AmqpExt\AmqpTransport;
use Symfony\Component\Messenger\Transport\AmqpExt\Connection;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Component\Messenger\Transport\Serialization\Serializer as MessengerSerializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateIntervalNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;

class TransportFactory
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

    public function createSender(): SenderInterface
    {
        $connection = $this->createConnection();
        $messengerSerializer = $this->createMessengerSerializer();

        $sender = new AmqpSender($connection, $messengerSerializer);

        return $sender;
    }

    public function createReceiver(): ReceiverInterface
    {
        $connection = $this->createConnection();
        $messengerSerializer = $this->createMessengerSerializer();

        $receiver = new AmqpReceiver($connection, $messengerSerializer);

        return $receiver;
    }

    private function createSerializer(): SymfonySerializer
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

    private function createConnection(): Connection
    {
        $connection = Connection::fromDsn($this->dsn, $this->options);

        return $connection;
    }

    private function createMessengerSerializer(): MessengerSerializer
    {
        $symfonySerializer = $this->createSerializer();
        $messengerSerializer = new MessengerSerializer($symfonySerializer);

        return $messengerSerializer;
    }
}
