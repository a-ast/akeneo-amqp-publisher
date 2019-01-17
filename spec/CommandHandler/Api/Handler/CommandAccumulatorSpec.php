<?php

namespace spec\Aa\AkeneoImport\CommandHandler\Api\Handler;

use Aa\AkeneoImport\CommandHandler\Api\Handler\CommandAccumulator;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Aa\AkeneoImport\CommandHandler\Api\Handler\fixture\TestCommand;

class CommandAccumulatorSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(3);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CommandAccumulator::class);
    }

    function it_adds_a_command()
    {
        $this->add('1', new TestCommand('1'));
    }

    function it_is_not_full_after_adding_an_existing_id()
    {
        $this->add('1', new TestCommand('1'));
        $this->add('2', new TestCommand('2'));
        $this->add('3', new TestCommand('3'));

        $this->isFullAfter('1')->shouldReturn(false);
    }

    function it_is_full_after_adding_a_new_id()
    {
        $this->add('1', new TestCommand('1'));
        $this->add('2', new TestCommand('2'));
        $this->add('3', new TestCommand('3'));

        $this->isFullAfter('4')->shouldReturn(true);
    }

    function it_returns_commands()
    {
        $commands = [
            ['1', new TestCommand('1')],
            ['1', new TestCommand('2')],
            ['2', new TestCommand('3')],
        ];

        foreach ($commands as $commandData) {
            $this->add($commandData[0], $commandData[1]);
        }

        $this->getCommands()->shouldBe(array_column($commands, 1));
    }

    function it_clears()
    {
        $this->add('1', new TestCommand('1'));
        $this->add('2', new TestCommand('2'));

        $this->clear();

        $this->getCommands()->shouldBeLike([]);
    }
}
