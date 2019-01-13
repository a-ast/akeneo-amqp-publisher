<?php

namespace spec\Aa\AkeneoImport\CommandHandler\Api;

use Aa\AkeneoImport\CommandHandler\Api\ApiRegistry;
use Aa\AkeneoImport\ImportCommand\Product\UpdateOrCreateProduct;
use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Akeneo\Pim\ApiClient\Api\ProductApiInterface;
use PhpParser\Node\Arg;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ApiRegistrySpec extends ObjectBehavior
{
    function let(AkeneoPimClientInterface $client, ProductApiInterface $productApi)
    {
        $client->getProductApi()->willReturn($productApi);

        $this->beConstructedWith($client);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ApiRegistry::class);
    }

    function it_returns_api(ProductApiInterface $productApi)
    {
        $this->getApi(UpdateOrCreateProduct::class)->shouldReturn($productApi);
    }
}
