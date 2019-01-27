<?php

namespace Aa\AkeneoImport\Queue;

use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Interop\Amqp\AmqpContext;
use Interop\Amqp\AmqpQueue;
use Interop\Queue\Context;
use Symfony\Component\Serializer\SerializerInterface;

class RemoteQueue implements CommandQueueInterface
{
    /**
     * @var \Interop\Queue\Context
     */
    private $context;

    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $queueName;

    /**
     * @var \Interop\Queue\Queue
     */
    private $queue;

    /**
     * @var \Interop\Queue\Producer
     */
    private $producer;

    /**
     * @var \Interop\Queue\Consumer
     */
    private $consumer;

    public function __construct(string $queueName, Context $context, SerializerInterface $serializer)
    {
        $this->queueName = $queueName;
        $this->context = $context;
        $this->serializer = $serializer;
    }

    public function enqueue(CommandInterface $command)
    {
        $this->initializeQueue(true);
        $this->initializeProducer();

        $body = $this->serializer->serialize($command, 'json');

        $headers = [
            'class' => get_class($command),
        ];

        $message = $this->context->createMessage($body, [], $headers);

        $this->producer->send($this->queue, $message);
    }

    public function dequeue(): ?CommandInterface
    {
        $this->initializeQueue(false);
        $this->initializeConsumer();

        $message = $this->consumer->receive(1);

        if (null === $message) {
            return null;
        }

        $body = $message->getBody();

        $commandClass = $message->getHeader('class');
        $command = $this->serializer->deserialize($body, $commandClass, 'json');

        if (!$command instanceof CommandInterface) {
            throw new \Exception(sprintf('Impossible to deserialize a command from %s', substr($body, 1, 1000)));
        }

        return $command;
    }

    private function initializeQueue(bool $declare)
    {
        if ($this->queue !== null) {
            return;
        }

        $this->queue = $this->context->createQueue($this->queueName);

        if (false === $declare) {
            return;
        }

        if ($this->queue instanceof AmqpQueue && $this->context instanceof AmqpContext) {
            $this->queue->addFlag(AmqpQueue::FLAG_DURABLE);
            $this->context->declareQueue($this->queue);
        }
    }

    private function initializeProducer()
    {
        if ($this->producer !== null) {
            return;
        }

        $this->producer = $this->context->createProducer();
    }

    private function initializeConsumer()
    {
        if ($this->consumer !== null) {
            return;
        }

        $this->consumer = $this->context->createConsumer($this->queue);
    }
}
