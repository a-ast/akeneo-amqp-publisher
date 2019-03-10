<?php

namespace spec\Aa\AkeneoImport\CommandHandler\Api\Handler;

use Aa\AkeneoImport\CommandHandler\Api\ResponseHandler;
use Aa\AkeneoImport\ImportCommand\CommandCallbacks;
use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Aa\AkeneoImport\fixture\TestCommand;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UpsertableHandlerSpec extends ObjectBehavior
{
    function let(UpsertableResourceListInterface $api, NormalizerInterface $normalizer, ResponseHandler $responseHandler)
    {
        $normalizer
            ->normalize(Argument::type(TestCommand::class), Argument::any(), Argument::any())
            ->will(function(array $commands) {
                $command = $commands[0];

                return array_merge(['identifier' => $command->getProductIdentifier()], $command->getAttributes());
            });

        $this->beConstructedWith($api, 'identifier', $normalizer, $responseHandler, 2);
    }


    function it_handles_one_command(UpsertableResourceListInterface $api)
    {
        $api->upsertList(Argument::type('array'))->shouldBeCalled()->willReturn([]);

        $this->handle(new TestCommand('1'));
        $this->tearDown();
    }

    function it_handles_commands_and_sends_data_in_batches(UpsertableResourceListInterface $api)
    {
        $api->upsertList(Argument::type('array'))->shouldBeCalledTimes(3)->willReturn([]);

        $this->handle(new TestCommand('1'));
        $this->handle(new TestCommand('2'));
        $this->handle(new TestCommand('3'));
        $this->handle(new TestCommand('4'));
        $this->handle(new TestCommand('5'));
        $this->tearDown();
    }

    function it_checks_upsert_response(UpsertableResourceListInterface $api, ResponseHandler $responseHandler, CommandCallbacks $callbacks)
    {
        $upsertResponse = [
            ['identifier' => 1, 'message' => 'Ok', 'status_code' => 200],
        ];
        $api->upsertList(Argument::type('array'))->willReturn($upsertResponse);

        $command = new TestCommand('1');
        $this->handle($command, $callbacks);
        $this->tearDown();

        $responseHandler->handleCommands([$command], 200, 'Ok', $callbacks, [])->shouldBeCalled();
    }
}
