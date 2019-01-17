<?php

namespace Aa\AkeneoImport\Transport;

use Aa\AkeneoImport\ImportCommand\CommandInterface;
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

    public function send(CommandInterface $command)
    {
        $producer = $this->context->createProducer();
        $queue = $this->context->createQueue('messages');

        if ($queue instanceof AmqpQueue && $this->context instanceof AmqpContext) {
            $queue->addFlag(AmqpQueue::FLAG_DURABLE);
            $this->context->declareQueue($queue);
        }

        $body = $this->serializer->serialize($command, 'json');

        $message = $this->context->createMessage($body);

        $producer->send($queue, $message);
    }
}
