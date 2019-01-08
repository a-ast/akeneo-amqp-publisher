<?php

namespace spec\Aa\AkeneoImport\Transport;

use Aa\AkeneoImport\Transport\Sender;
use Aa\AkeneoImport\Transport\TransportFactory;
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
