<?php

namespace Aa\AkeneoImport\CommandBus\Transport;

use Aa\AkeneoImport\ImportCommand\CommandBatchInterface;
use Enqueue\AmqpExt\AmqpContext;
use Interop\Amqp\Impl\AmqpQueue;
use Interop\Queue\Context;
use Symfony\Component\Serializer\SerializerInterface;

class Sender
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

    public function send(CommandBatchInterface $commandBatch)
    {
        $producer = $this->context->createProducer();
        $queue = $this->context->createQueue($commandBatch->getCommandClass());
        $queue->addFlag(AmqpQueue::FLAG_DURABLE);

        if ($this->context instanceof AmqpContext) {
            $this->context->declareQueue($queue);
        }

        $body = $this->serializer->serialize($commandBatch, 'json');

        $message = $this->context->createMessage($body);

        $producer->send($queue, $message);
    }
}
