<?php

namespace Aa\AkeneoImport\CommandBus\Transport;

use Aa\AkeneoImport\ImportCommand\CommandListInterface;
use Enqueue\AmqpExt\AmqpContext;
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

    public function send(CommandListInterface $commandList)
    {
        $producer = $this->context->createProducer();
        $queue = $this->context->createQueue($commandList->getCommandClass());

        if ($this->context instanceof AmqpContext) {
            $this->context->declareQueue($queue);
        }

        $body = $this->serializer->serialize($commandList, 'json');

        $message = $this->context->createMessage($body);

        $producer->send($queue, $message);
    }
}
