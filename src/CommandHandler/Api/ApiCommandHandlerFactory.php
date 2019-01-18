<?php

namespace Aa\AkeneoImport\CommandHandler\Api;

use Aa\AkeneoImport\CommandHandler\Api\Handler\DeleteHandler;
use Aa\AkeneoImport\CommandHandler\Api\Handler\MediaHandler;
use Aa\AkeneoImport\CommandHandler\Api\Handler\UpsertableHandler;
use Aa\AkeneoImport\ImportCommand\Control\FinishImport;
use Aa\AkeneoImport\ImportCommand\Media\CreateProductMediaFile;
use Aa\AkeneoImport\ImportCommand\Media\CreateProductModelMediaFile;
use Aa\AkeneoImport\ImportCommand\Product\DeleteProduct;
use Aa\AkeneoImport\ImportCommand\Product\ProductFieldInterface;
use Aa\AkeneoImport\ImportCommand\ProductModel\ProductModelFieldInterface;
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
    public function createByApiClient(AkeneoPimClientInterface $client): array
    {
        $normalizer = $this->createSerializer();

        $upsertableProductHandler = new UpsertableHandler(
            $client->getProductApi(),
            $normalizer
        );

        $upsertableProductModelHandler = new UpsertableHandler(
            $client->getProductModelApi(),
            $normalizer
        );


        return [
            DeleteProduct::class => new DeleteHandler($client->getProductApi()),
            CreateProductMediaFile::class => new MediaHandler($client->getProductMediaFileApi()),
            CreateProductModelMediaFile::class => new MediaHandler($client->getProductMediaFileApi()),
            ProductFieldInterface::class => $upsertableProductHandler,
            ProductModelFieldInterface::class => $upsertableProductModelHandler,
            FinishImport::class => $upsertableProductModelHandler,
        ];
    }

    public function createByCredentials(string $baseUri, string $clientId, string $secret, string $username, string $password): array
    {
        $clientBuilder = new AkeneoPimClientBuilder($baseUri);
        $client = $clientBuilder->buildAuthenticatedByPassword($clientId, $secret, $username, $password);

        return $this->createByApiClient($client);
    }

    private function createSerializer(): NormalizerInterface
    {
        $normalizers = [
            new DateTimeNormalizer(),
            new DateIntervalNormalizer(),
            new ArrayDenormalizer(),
            new ObjectNormalizer(),
        ];

        $serializer = new Serializer($normalizers, []);

        return $serializer;
    }
}
