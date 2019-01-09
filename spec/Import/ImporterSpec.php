<?php

namespace spec\Aa\AkeneoImport\Import;

use Aa\AkeneoImport\CommandBus\CommandBusFactory;
use Aa\AkeneoImport\Import\Importer;
use Aa\AkeneoImport\ImportCommand\CommandHandlerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class ImporterSpec extends ObjectBehavior
{
    function let(CommandBusFactory $factory,
        CommandHandlerInterface $handler,
        MessageBusInterface $bus
    ) {
        $factory->createCommandBus($handler)->willReturn($bus);
        $bus->dispatch(Argument::any())->willReturn(new Envelope(new class {}));

        $this->beConstructedWith($factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Importer::class);
    }

    function it_imports_array_of_command(CommandHandlerInterface $handler)
    {
        $this->import([], $handler);
    }

    function it_imports_commands_provided_by_generator(CommandHandlerInterface $handler)
    {
        $generator = new class {

            public function getCommands(): iterable
            {
                yield new class {};
            }
        };

        $this->import($generator->getCommands(), $handler);
    }
}
