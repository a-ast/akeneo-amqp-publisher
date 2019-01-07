<?php

namespace Aa\AkeneoImport\CommandHandler\Api\ApiAdapter;

use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\Response;
use Akeneo\Pim\ApiClient\Api\MediaFileApiInterface;
use Akeneo\Pim\ApiClient\Exception\HttpException;
use ArrayObject;
use Traversable;

class MediaApiAdapter implements ApiAdapterInterface
{
    /**
     * @param MediaFileApiInterface $api
     */
    public function send($api, array $data): Traversable
    {
        $errors = [];

        foreach ($data as $resource) {
            try {
                $api->create($resource['media'], $resource['meta']);
            } catch (HttpException $e) {

                $errors[] = new Response([
                    'status_code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'data' => $resource,
                ]);
            }
        }

        return new ArrayObject($errors);
    }

    public function supportsApi($api)
    {
        return $api instanceof MediaFileApiInterface;
    }
}
