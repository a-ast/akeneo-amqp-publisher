<?php

namespace spec\Aa\AkeneoImport\CommandHandler\Api;

use Aa\AkeneoImport\CommandHandler\Api\Handler\ResponseHandler;
use Aa\AkeneoImport\ImportCommand\CommandCallbacks;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResponseHandlerSpec extends ObjectBehavior
{
    function it_calls_repeat_for_failed_but_recoverable_commands(CommandInterface $command, CommandCallbacks $callbacks)
    {
        $this->handle($command, 422, 'Property "parent" expects a valid parent code.', $callbacks);

        $callbacks->repeat($command)->shouldBeCalled();
    }

    function it_calls_repeat_for_failed_but_recoverable_commands_by_regexp_in_message(CommandInterface $command, CommandCallbacks $callbacks)
    {
        $this->handle($command, 422, 'Product model "zzz" does not exist.', $callbacks);

        $callbacks->repeat($command)->shouldBeCalled();
    }

    function it_skips_successful_commands(CommandInterface $command, CommandCallbacks $callbacks)
    {
        $this->handle($command, 201, '', $callbacks);

        $callbacks->repeat(Argument::any())->shouldNotBeCalled();
        $callbacks->reject(Argument::any())->shouldNotBeCalled();
    }

}
