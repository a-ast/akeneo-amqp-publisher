<?php

namespace Aa\AkeneoImport\CommandBus\Transport;

use Aa\AkeneoImport\ImportCommand\CommandList;
use Aa\AkeneoImport\ImportCommand\Exception\RecoverableCommandHandlerException;
use Interop\Amqp\Impl\AmqpQueue;
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

    public function receive(string $queueName): \Generator
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
            $commandList = $this->serializer->deserialize($body, CommandList::class, 'json');

            try {
                yield $commandList;

                $consumer->acknowledge($message);

            } catch (RecoverableCommandHandlerException $e) {

                // true for requeue
                $consumer->reject($message, true);

                yield;

            } catch (\Exception $e) {

                // true for requeue
                $consumer->reject($message, false);

                yield;
            }

        }
    }
}
