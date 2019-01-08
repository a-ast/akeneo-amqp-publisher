<?php

namespace spec\Aa\AkeneoImport\Transport;

use Aa\AkeneoImport\CommandBus\Consumer;
use Aa\AkeneoImport\Transport\Receiver;
use Aa\AkeneoImport\ImportCommand\CommandBatchInterface;
use Interop\Queue\Context;
use Interop\Queue\Queue;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Serializer\SerializerInterface;

class ReceiverSpec extends ObjectBehavior
{
    function let(Context $context, SerializerInterface $serializer)
    {
        $this->beConstructedWith($context, $serializer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Receiver::class);
    }

    function it_receives_command_batch(CommandBatchInterface $commandBatch, Context $context,
        SerializerInterface $serializer, Consumer $consumer, Queue $queue
    ) {
        $queueName = 'Aa\\Commands\\Command';
        $commandBatch->getCommandClass()->willReturn($queueName);

        $serializer->deserialize(Argument::any(), $queueName, 'json')->willReturn($commandBatch);

        $context->createQueue(Argument::type('string'))->willReturn($queue);
        $context->createConsumer($queue)->willReturn($consumer);

        $this->receive($queueName)->shouldBeAnInstanceOf(\Generator::class);
    }
}
