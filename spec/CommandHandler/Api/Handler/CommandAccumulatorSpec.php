<?php

namespace spec\Aa\AkeneoImport\CommandHandler\Api\Handler;

use Aa\AkeneoImport\CommandHandler\Api\Handler\CommandAccumulator;
use PhpSpec\ObjectBehavior;

class CommandAccumulatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CommandAccumulator::class);
    }

    function it_adds_a_command()
    {
        $this->add('1', []);
    }

    function it_returns_count_after_adding_a_command_with_existing_code()
    {
        $this->add('2', []);
        $this->add('3', []);
        $this->add('1', []);

        $this->getCountAfterAdding(3)->shouldReturn(3);
    }

    function it_returns_count_after_adding_a_command_with_new_code()
    {
        $this->add(1, []);
        $this->add(2, []);
        $this->add(3, []);

        $this->getCountAfterAdding(4)->shouldReturn(4);
    }

    function it_returns_acccumulated_data()
    {
        $commands = [
            ['identifier' => '1', 'color' => 'red', 'width' => 1],
            ['identifier' => '2', 'color' => 'green', 'height' => 2],
            ['identifier' => '1', 'depth' => 3],
            ['identifier' => '2', 'form' => 'round'],
            ['identifier' => '3', 'color' => 'blue'],
        ];

        foreach ($commands as $commandData) {
            $this->add($commandData['identifier'], $commandData);
        }

        $this->getAccumulatedData()->shouldBe([
            '1' => ['identifier' => '1', 'color' => 'red', 'width' => 1, 'depth' => 3],
            '2' => ['identifier' => '2', 'color' => 'green', 'height' => 2, 'form' => 'round'],
            '3' => ['identifier' => '3', 'color' => 'blue']
        ]);
    }

    function it_clears()
    {
        $this->add('1', ['color' => 'red']);
        $this->add('2', ['color' => 'blue']);

        $this->clear();

        $this->getAccumulatedData()->shouldBeLike([]);
    }
}
