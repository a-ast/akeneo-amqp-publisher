<?php

namespace spec\Aa\AkeneoImport\CommandHandler\Api\Handler;

use Aa\AkeneoImport\CommandHandler\Api\Handler\ResponseHandler;
use Aa\AkeneoImport\ImportCommand\CommandCallbacks;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResponseHandlerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ResponseHandler::class);
    }

    function it_calls_repeat_for_failed_but_recoverable_commands(CommandInterface $command, CommandCallbacks $callbacks)
    {
        $this->handle($command, $callbacks, 422, 'Property "parent" expects a valid parent code.');

        $callbacks->repeat($command)->shouldBeCalled();
    }

    function it_skips_successful_commands(CommandInterface $command, CommandCallbacks $callbacks)
    {
        $this->handle($command, $callbacks, 201, '');

        $callbacks->repeat(Argument::any())->shouldNotBeCalled();
        $callbacks->reject(Argument::any())->shouldNotBeCalled();
    }

}
