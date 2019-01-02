<?php

namespace spec\Aa\AkeneoImport\CommandBus\Transport;

use Aa\AkeneoImport\CommandBus\Consumer;
use Aa\AkeneoImport\CommandBus\Transport\Receiver;
use Aa\AkeneoImport\ImportCommand\CommandListInterface;
use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Producer;
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

    function it_receives_command_lists(CommandListInterface $commandList, Context $context,
        SerializerInterface $serializer, Consumer $consumer, Queue $queue, Message $message
    ) {
        $queueName = 'Aa\\Commands\\Command';
        $commandList->getCommandClass()->willReturn($queueName);

        $serializer->deserialize(Argument::any(), $queueName, 'json')->willReturn($commandList);

        $context->createQueue(Argument::type('string'))->willReturn($queue);
        $context->createConsumer($queue)->willReturn($consumer);

        $this->receive($queueName)->shouldBeAnInstanceOf(\Generator::class);
    }
}
