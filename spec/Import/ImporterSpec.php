<?php

namespace spec\Aa\AkeneoImport\Import;

use Aa\AkeneoImport\CommandBus\CommandBus;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Exception\RecoverableCommandHandlerException;
use Aa\AkeneoImport\Queue\CommandQueueInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ImporterSpec extends ObjectBehavior
{
    function let(CommandBus $commandBus)
    {
        $this->beConstructedWith($commandBus);
    }

    function it_imports_command_from_array(CommandBus $commandBus)
    {
        $command1 = new Command(1);
        $command2 = new Command(2);

        $commandBus->setUp()->shouldBeCalled();
        $commandBus->dispatch($command1)->shouldBeCalled();
        $commandBus->dispatch($command2)->shouldBeCalled();
        $commandBus->tearDown()->shouldBeCalled();

        $this->import([$command1, $command2]);
    }

    function it_imports_command_from_queue(CommandBus $commandBus, CommandQueueInterface $queue)
    {
        $command1 = new Command(1);
        $command2 = new Command(2);

        $queue->dequeue()->willReturn($command1, $command2, null);

        $commandBus->setUp()->shouldBeCalled();
        $commandBus->dispatch($command1)->shouldBeCalled();
        $commandBus->dispatch($command2)->shouldBeCalled();
        $commandBus->tearDown()->shouldBeCalled();

        $this->import([$command1, $command2]);
    }


    function it_republishes_failed_recoverable_commands(CommandBus $commandBus)
    {
        $command1 = new Command(1);
        $command2 = new Command(2);
        $command3 = new Command(3);

        $commandBus->setUp()->shouldBeCalled();
        $commandBus->tearDown()->shouldBeCalled();

        $exception = new RecoverableCommandHandlerException('Recover', 0, [$command2]);
        $commandBus->dispatch($command1)->shouldBeCalledTimes(1);
        $commandBus->dispatch($command2)->shouldBeCalledTimes(2);
        $commandBus->dispatch($command3)->shouldBeCalledTimes(1)->willThrow($exception);

        $this->import([$command1, $command2, $command3]);
    }

    function it_does_not_republish_failed_unrecoverable_commands(CommandBus $commandBus)
    {
        $command = new Command(1);

        $commandBus->setUp()->shouldBeCalled();
        $commandBus->tearDown()->shouldBeCalled();

        $commandBus->dispatch($command)
            ->shouldBeCalledTimes(1)
            ->willThrow(CommandHandlerException::class);

        $this->import([$command]);
    }

    function it_stops_for_all_other_exceptions(CommandBus $commandBus, \Exception $exception)
    {
        $command = new Command(1);

        $commandBus->setUp()->shouldBeCalled();
        $commandBus->tearDown()->shouldNotBeCalled();

        $commandBus->dispatch($command)->willThrow($exception->getWrappedObject());

        $this->shouldThrow($exception->getWrappedObject())->during('import', [[$command]]);
    }
}

class Command implements CommandInterface
{
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
};

