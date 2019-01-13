<?php

namespace spec\Aa\AkeneoImport\CommandHandler\Api;

use Aa\AkeneoImport\CommandHandler\Api\ApiAdapter\ApiAdapterInterface;
use Aa\AkeneoImport\CommandHandler\Api\ApiAdapterRegistry;
use Aa\AkeneoImport\ImportCommand\Product\UpdateOrCreateProduct;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ApiAdapterRegistrySpec extends ObjectBehavior
{
    function let(ApiAdapterInterface $upsertAdapter)
    {
        $this->beConstructedWith(
            [
                'upsert' => $upsertAdapter
            ]
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ApiAdapterRegistry::class);
    }

    function it_returns_api_adapter(ApiAdapterInterface $upsertAdapter)
    {
        $this->getApiAdapter(UpdateOrCreateProduct::class)->shouldReturn($upsertAdapter);
    }
}
