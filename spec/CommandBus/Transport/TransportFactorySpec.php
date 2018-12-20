<?php

namespace spec\Aa\AkeneoImport\CommandBus\Transport;

use Aa\AkeneoImport\CommandBus\Transport\Sender;
use Aa\AkeneoImport\CommandBus\Transport\TransportFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TransportFactorySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('amqp:', []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TransportFactory::class);
    }

    function it_creates_sender()
    {
        $this->createSender()->shouldReturnAnInstanceOf(Sender::class);
    }
}
