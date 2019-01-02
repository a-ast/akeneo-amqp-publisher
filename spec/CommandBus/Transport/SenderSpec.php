<?php

namespace spec\Aa\AkeneoImport\CommandBus\Transport;

use Aa\AkeneoImport\CommandBus\Transport\Sender;
use Aa\AkeneoImport\ImportCommand\CommandListInterface;
use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Producer;
use Interop\Queue\Queue;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Serializer\SerializerInterface;

class SenderSpec extends ObjectBehavior
{
    function let(Context $context, SerializerInterface $serializer)
    {
        $this->beConstructedWith($context, $serializer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Sender::class);
    }

    function it_sends_command_lists(CommandListInterface $commandList, Context $context,
        SerializerInterface $serializer, Producer $producer, Queue $queue, Message $message
    ) {
        $commandList->getCommandClass()->willReturn('Aa\\Commands\\Command');

        $serializer->serialize($commandList, 'json')->willReturn('serialized message');

        $context->createProducer()->willReturn($producer);
        $context->createQueue(Argument::type('string'))->willReturn($queue);
        $context->createMessage(Argument::type('string'))->willReturn($message);

        $this->send($commandList);
    }
}
