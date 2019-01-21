<?php

namespace spec\Aa\AkeneoImport\CommandHandler\Api\Handler;

use Aa\AkeneoImport\CommandHandler\Api\Handler\CommandDataMerger;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CommandDataMergerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CommandDataMerger::class);
    }

    function it_merges_command_data()
    {
        $data = [
            ['old-code' => 1, 'value-1' => 'a'],
            ['old-code' => 2, 'value-1' => 'b'],
            ['old-code' => 1, 'value-2' => 'c'],
            ['old-code' => 2, 'value-2' => 'd'],
            ['old-code' => 3, 'value-3' => 'e'],
        ];

        $this
            ->merge($data, 'old-code', 'code')
            ->shouldBeLike([
                ['code' => 1, 'value-1' => 'a', 'value-2' => 'c'],
                ['code' => 2, 'value-1' => 'b', 'value-2' => 'd'],
                ['code' => 3, 'value-3' => 'e'],
            ])
        ;
    }
}
