<?php

namespace spec\Aa\AkeneoImport\CommandBus;

use Aa\AkeneoImport\CommandBus\CommandPromise;
use Aa\AkeneoImport\ImportCommand\CommandHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\InitializableCommandHandlerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CommandBusSpec extends ObjectBehavior
{
    function it_redirects_commands_to_handlers(CommandHandlerInterface $handler1, CommandHandlerInterface $handler2)
    {
        $command1 = new class implements CommandInterface {};
        $command2 = new class implements CommandInterface {};

        $handlers = [
            get_class($command1) => $handler1,
            get_class($command2) => $handler2,
        ];

        $this->beConstructedWith($handlers);

        $handler1->handle($command1, null)->shouldBeCalled();
        $handler2->handle($command2, null)->shouldBeCalled();

        $this->dispatch($command1);
        $this->dispatch($command2);
    }

    function it_redirects_commands_to_handlers_by_interface(CommandHandlerInterface $handler)
    {
        $command = new class implements CommandInterface, CommonCommandInterface {};

        $handlers = [
            CommonCommandInterface::class => $handler,
        ];

        $this->beConstructedWith($handlers);

        $handler->handle($command, null)->shouldBeCalled();

        $this->dispatch($command);
    }

    function it_initializes_handlers_that_support_it(CommandHandlerInterface $handler)
    {
        $handler->implement(InitializableCommandHandlerInterface::class);

        $command = new class implements CommandInterface {};

        $handlers = [
            get_class($command) => $handler,
        ];

        $this->beConstructedWith($handlers);

        $handler->setUp()->shouldBeCalled();

        $this->setUp();
    }

    function it_finalizes_handlers_that_support_it(CommandHandlerInterface $handler)
    {
        $handler->implement(InitializableCommandHandlerInterface::class);

        $command = new class implements CommandInterface {};

        $handlers = [
            get_class($command) => $handler,
        ];

        $this->beConstructedWith($handlers);

        $handler->tearDown()->shouldBeCalled();

        $this->tearDown();
    }

    function it_throws_an_exception_if_handler_not_found(CommandHandlerInterface $handler)
    {
        $command = new class implements CommandInterface {};

        $handlers = [
            UnknownCommandInterface::class => $handler,
        ];

        $this->beConstructedWith($handlers);

        $this->shouldThrow(CommandHandlerException::class)->during('dispatch', [$command]);
    }
}

interface CommonCommandInterface {}

interface UnknownCommandInterface {}
