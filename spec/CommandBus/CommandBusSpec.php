<?php

namespace spec\Aa\AkeneoImport\CommandBus;

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

        $handler1->handle($command1)->shouldBeCalled();
        $handler2->handle($command2)->shouldBeCalled();

        $this->dispatch([$command1, $command2]);
    }

    function it_redirects_commands_to_handlers_by_interface(CommandHandlerInterface $handler)
    {
        $command = new class implements CommandInterface, CommonCommandInterface {};

        $handlers = [
            CommonCommandInterface::class => $handler,
        ];

        $this->beConstructedWith($handlers);

        $handler->handle($command)->shouldBeCalled();

        $this->dispatch([$command]);
    }

    /**
     * Note: not possible to use interface for the parameter $handler because of the bug:
     * https://github.com/phpspec/prophecy/issues/192
     * Because of that, there is a fake class InitializableCommandHandler
     */
    function it_initialize_and_finalize_handlers_that_support_it(InitializableCommandHandler $handler)
    {
        $command = new class implements CommandInterface {};

        $handlers = [
            get_class($command) => $handler,
        ];

        $this->beConstructedWith($handlers);

        $handler->setUp()->shouldBeCalled();
        $handler->tearDown()->shouldBeCalled();
        $handler->handle($command)->shouldBeCalled();

        $this->dispatch([$command]);
    }

    function it_dispatches_commands_provided_by_generator(CommandHandlerInterface $handler)
    {
        $handlers = [
            Command::class => $handler,
        ];

        $this->beConstructedWith($handlers);

        $generator = new class {

            public function getCommands(): iterable
            {
                yield new Command();
            }
        };

        $this->dispatch($generator->getCommands());
    }

    function it_throws_an_exception_if_handler_not_found(CommandHandlerInterface $handler)
    {
        $command = new class implements CommandInterface {};

        $handlers = [
            UnknownCommandInterface::class => $handler,
        ];

        $this->beConstructedWith($handlers);

        $this->shouldThrow(CommandHandlerException::class)->during('dispatch', [[$command]]);
    }
}

interface CommonCommandInterface {}

interface UnknownCommandInterface {}

class Command implements CommandInterface {};

class InitializableCommandHandler implements InitializableCommandHandlerInterface
{
    public function setUp() {}

    public function tearDown() {}

    public function handle(CommandInterface $command) {}
}
