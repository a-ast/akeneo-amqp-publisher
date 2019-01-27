<?php

namespace spec\Aa\AkeneoImport\Queue;

use Aa\AkeneoImport\Queue\CommandQueueInterface;
use Aa\AkeneoImport\Queue\RemoteQueue;
use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Producer;
use Interop\Queue\Queue;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Aa\AkeneoImport\fixture\TestCommand;
use Symfony\Component\Serializer\SerializerInterface;

class RemoteQueueSpec extends ObjectBehavior
{
    function let(Context $context, SerializerInterface $serializer)
    {
        $this->beConstructedWith('messages', $context, $serializer);
    }

    function it_is_a_command_queue()
    {
        $this->shouldImplement(CommandQueueInterface::class);
    }

    function it_enqueues_commands(Context $context, SerializerInterface $serializer,
        Queue $queue, Producer $producer, Message $message)
    {
        $serializer->serialize(Argument::any(), 'json')->willReturn('');

        $context->createQueue('messages')->willReturn($queue);
        $context->createProducer()->willReturn($producer);
        $context->createMessage(Argument::any(), Argument::any(), Argument::any())->willReturn($message);

        $producer->send($queue, $message)->shouldBeCalled();

        $this->enqueue(new TestCommand('1'));
    }
}
