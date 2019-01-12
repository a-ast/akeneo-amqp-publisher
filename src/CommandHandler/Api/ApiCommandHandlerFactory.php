<?php

namespace Aa\AkeneoImport\CommandHandler\Api;

use Aa\AkeneoImport\CommandHandler\Api\ApiAdapter\DeleteApiAdapter;
use Aa\AkeneoImport\CommandHandler\Api\ApiAdapter\MediaApiAdapter;
use Aa\AkeneoImport\CommandHandler\Api\ApiAdapter\UpsertableApiAdapter;
use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\Validator;
use Aa\AkeneoImport\Normalizer\CommandBatchNormalizer;
use Aa\AkeneoImport\Normalizer\CommandNormalizer;
use Akeneo\Pim\ApiClient\AkeneoPimClientBuilder;
use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateIntervalNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiCommandHandlerFactory
{
    public function createByApiClient(AkeneoPimClientInterface $client): ApiCommandHandler
    {
        $normalizer = $this->createSerializer();

        $apiAdapters = [
            'upsertable' => new UpsertableApiAdapter($normalizer),
            'media' => new MediaApiAdapter(),
            'delete' => new DeleteApiAdapter(),
        ];

        $validator = new Validator();

        return new ApiCommandHandler($client, $normalizer, $apiAdapters, $validator);
    }

    public function createByCredentials(string $baseUri, string $clientId, string $secret, string $username, string $password): ApiCommandHandler
    {
        $clientBuilder = new AkeneoPimClientBuilder($baseUri);
        $client = $clientBuilder->buildAuthenticatedByPassword($clientId, $secret, $username, $password);

        return $this->createByApiClient($client);
    }

    private function createSerializer(): NormalizerInterface
    {
        $normalizers = [
            new CommandBatchNormalizer(),
            new CommandNormalizer(),
            new DateTimeNormalizer(),
            new DateIntervalNormalizer(),
            new ArrayDenormalizer(),
            new ObjectNormalizer(),
        ];

        $serializer = new Serializer($normalizers, []);

        return $serializer;
    }
}
