<?php

namespace spec\Aa\AkeneoImport\CommandHandler\Api\Handler;

use Aa\AkeneoImport\CommandHandler\Api\Handler\CommandAccumulator;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Aa\AkeneoImport\CommandHandler\Api\Handler\fixture\TestCommand;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CommandAccumulatorSpec extends ObjectBehavior
{
    function let(NormalizerInterface $normalizer)
    {
        $normalizer
            ->normalize(Argument::type(TestCommand::class), Argument::any(), Argument::any())
            ->will(function(array $commands) {
                $command = $commands[0];

                return array_merge(['identifier' => $command->getProductIdentifier()], $command->getAttributes());
            })
        ;

        $this->beConstructedWith($normalizer, 'identifier');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CommandAccumulator::class);
    }

    function it_adds_a_command()
    {
        $command = new TestCommand('1');

        $this->add($command);
    }

    function it_returns_count_after_adding_a_command_with_existing_code()
    {
        $this->add(new TestCommand('2'));
        $this->add(new TestCommand('3'));
        $this->add(new TestCommand('1'));

        $this->getCountAfterAdding(new TestCommand('1'))->shouldReturn(3);
    }

    function it_returns_count_after_adding_a_command_with_new_code()
    {
        $this->add(new TestCommand('1'));
        $this->add(new TestCommand('2'));
        $this->add(new TestCommand('3'));

        $this->getCountAfterAdding(new TestCommand('4'))->shouldReturn(4);
    }

    function it_returns_acccumulated_data()
    {
        $commands = [
            new TestCommand('1', ['color' => 'red', 'width' => 1]),
            new TestCommand('2', ['color' => 'green', 'height' => 2]),
            new TestCommand('1', ['depth' => 3]),
            new TestCommand('2', ['form' => 'round']),
            new TestCommand('3', ['color' => 'blue']),
        ];

        foreach ($commands as $command) {
            $this->add($command);
        }

        $this->getAccumulatedData()->shouldBe([
            '1' => ['identifier' => '1', 'color' => 'red', 'width' => 1, 'depth' => 3],
            '2' => ['identifier' => '2', 'color' => 'green', 'height' => 2, 'form' => 'round'],
            '3' => ['identifier' => '3', 'color' => 'blue']
        ]);
    }

    function it_clears()
    {
        $this->add(new TestCommand('1'));
        $this->add(new TestCommand('2'));

        $this->clear();

        $this->getCommands()->shouldBeLike([]);
    }
}
