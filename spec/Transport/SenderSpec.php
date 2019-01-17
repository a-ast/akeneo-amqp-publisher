<?php

namespace spec\Aa\AkeneoImport\Transport;

use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\Transport\Sender;
use Interop\Amqp\AmqpQueue;
use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Producer;
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

    function it_sends_command_batch(CommandInterface $command, Context $context,
        SerializerInterface $serializer, Producer $producer, AmqpQueue $queue, Message $message
    ) {
        $serializer->serialize($command, 'json')->willReturn('serialized message');

        $context->createProducer()->willReturn($producer);
        $context->createQueue(Argument::type('string'))->willReturn($queue);

        $context->createMessage(Argument::type('string'))->willReturn($message);

        $this->send($command);
    }
}
