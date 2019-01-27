<?php

namespace Aa\AkeneoImport\Queue;

use Enqueue\AmqpExt\AmqpConnectionFactory;
use Interop\Queue\Context;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateIntervalNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;


class RemoteQueueFactory
{
    /**
     * @var string
     */
    private $dsn;

    /**
     * @var Context
     */
    private $context;

    public function __construct(string $dsn)
    {
        $this->dsn = $dsn;
    }

    public function create(string $queueName): CommandQueueInterface
    {
        $this->initializeContext();

        $queue = new RemoteQueue($queueName, $this->context, $this->createSerializer());

        return $queue;
    }

    private function createSerializer(): SerializerInterface
    {
        $normalizers = [
            new JsonSerializableNormalizer(),
            new DateTimeNormalizer(),
            new DateIntervalNormalizer(),
            new ArrayDenormalizer(),
            new ObjectNormalizer(),
        ];

        $encoders = [
            new JsonEncoder(),
        ];

        $serializer = new Serializer($normalizers, $encoders);

        return $serializer;
    }

    private function initializeContext(): void
    {
        if (null !== $this->context) {
            return;
        }

        $factory = new AmqpConnectionFactory($this->dsn);
        $this->context = $factory->createContext();
    }
}
