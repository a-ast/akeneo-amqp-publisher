<?php

namespace spec\Aa\AkeneoImport\Queue;

use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\Queue\CommandQueueInterface;
use Aa\AkeneoImport\Queue\InMemoryQueue;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InMemoryQueueSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(InMemoryQueue::class);
        $this->shouldImplement(CommandQueueInterface::class);
    }

    function it_can_be_created_from_iterable(CommandInterface $command)
    {
        $this->beConstructedWith([$command]);

        $this->dequeue()->shouldReturn($command);
    }

    function it_enqueues_a_command_and_it_can_be_dequeued(CommandInterface $command)
    {
        $this->enqueue($command);

        $this->dequeue()->shouldReturn($command);
    }

    function it_dequeues_null_if_queue_is_empty(CommandInterface $command)
    {
        $this->enqueue($command);

        $this->dequeue();
        $this->dequeue()->shouldReturn(null);
    }
}
