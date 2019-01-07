<?php

namespace spec\Aa\AkeneoImport\CommandHandler\Api\ApiAdapter;

use Aa\AkeneoImport\CommandHandler\Api\ApiAdapter\ApiAdapterInterface;
use Aa\AkeneoImport\CommandHandler\Api\ApiAdapter\UpsertableApiAdapter;
use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\Response;
use Aa\AkeneoImport\ImportCommand\Exception\RecoverableCommandHandlerException;
use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UpsertableApiAdapterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(UpsertableApiAdapter::class);
        $this->shouldBeAnInstanceOf(ApiAdapterInterface::class);
    }

    function it_sends_data_to_api(UpsertableResourceListInterface $api)
    {
        $apiResponse = new \ArrayObject([
            ['identifier' => '1', 'status_code' => 201],
            ['identifier' => '2', 'status_code' => 201],
        ]);

        $response = new \ArrayObject([
            new Response(['identifier' => '1', 'status_code' => 201]),
            new Response(['identifier' => '2', 'status_code' => 201]),
        ]);

        $api->upsertList([])->willReturn($apiResponse);

        $this->send($api, [])->shouldIterateLike($response);
    }

    function it_supports_api(UpsertableResourceListInterface $api, DummyInterface $otherApi)
    {
        $this->supportsApi($api)->shouldBeEqualTo(true);
        $this->supportsApi($otherApi)->shouldBeEqualTo(false);
    }
}

interface DummyInterface
{

}
