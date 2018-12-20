<?php

namespace Aa\AkeneoImport\CommandBus\Transport;

use Aa\AkeneoImport\ImportCommands\CommandListInterface;
use Interop\Queue\Context;
use Symfony\Component\Serializer\SerializerInterface;

class Receiver
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(Context $context, SerializerInterface $serializer)
    {
        $this->context = $context;
        $this->serializer = $serializer;
    }

    public function receive(string $queueName): iterable
    {
        $queue = $this->context->createQueue($queueName);

        $consumer = $this->context->createConsumer($queue);

        $isConsuming = true;

        while ($isConsuming) {
            $message = $consumer->receive(1);

            if (null === $message) {
                $isConsuming = false;

                continue;
            }

            $body = $message->getBody();
            $commandList = $this->serializer->deserialize($body, CommandListInterface::class, 'json');

            try {
                yield $commandList;

                $consumer->acknowledge($message);

            } catch (\Exception $e) {

                // true for requeue
                $consumer->reject($message, false);

            }

        }
    }
}
