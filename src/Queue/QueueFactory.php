<?php

namespace Aa\AkeneoImport\Queue;

use Enqueue\AmqpExt\AmqpConnectionFactory;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateIntervalNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;


class QueueFactory
{
    public function createByDsn(string $dsn, string $queueName): CommandQueueInterface
    {
        $factory = new AmqpConnectionFactory($dsn);
        $context = $factory->createContext();

        $queue = new RemoteQueue($queueName, $context, $this->createSerializer());

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
}
