<?php

namespace Aa\AkeneoImport\CommandHandler\Api\ApiAdapter;

use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\Response;
use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;
use Traversable;

class UpsertableApiAdapter implements ApiAdapterInterface
{
    /**
     * @param UpsertableResourceListInterface $api
     */
    public function send($api, array $data): Traversable
    {
        $upsertedResources = $api->upsertList($data);

        $responses = [];

        foreach ($upsertedResources as $upsertedResource) {
            $responses[] = new Response($upsertedResource);
        }

        return new \ArrayObject($responses);
    }

    public function supportsApi($api)
    {
        return $api instanceof UpsertableResourceListInterface;
    }
}
