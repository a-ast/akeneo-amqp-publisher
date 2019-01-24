<?php

namespace Aa\AkeneoImport\Import;

use Aa\AkeneoImport\CommandBus\CommandBus;
use Aa\AkeneoImport\CommandHandler\Api\Handler\DeleteHandler;
use Aa\AkeneoImport\CommandHandler\Api\Handler\MediaHandler;
use Aa\AkeneoImport\CommandHandler\Api\Handler\UpsertableHandler;
use Aa\AkeneoImport\CommandHandler\Normalizer\CommandNormalizer;
use Aa\AkeneoImport\ImportCommand\Media\CreateProductMediaFile;
use Aa\AkeneoImport\ImportCommand\Media\CreateProductModelMediaFile;
use Aa\AkeneoImport\ImportCommand\Product\DeleteProduct;
use Aa\AkeneoImport\ImportCommand\Product\ProductFieldInterface;
use Aa\AkeneoImport\ImportCommand\ProductModel\ProductModelFieldInterface;
use Aa\AkeneoImport\Queue\InMemoryQueue;
use Akeneo\Pim\ApiClient\AkeneoPimClientBuilder;
use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateIntervalNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiImporterFactory
{
    public function createByApiClient(AkeneoPimClientInterface $client): ImporterInterface
    {
        $propertyReplacementMap = [
            'product_identifier' => 'identifier',
            'product_model_code' => 'code',
            'categoryCode' => 'code',
        ];

        $normalizer = $this->createSerializer($propertyReplacementMap);

        $upsertableProductHandler = new UpsertableHandler(
            $client->getProductApi(),
            'identifier',
            $normalizer
        );

        $upsertableProductModelHandler = new UpsertableHandler(
            $client->getProductModelApi(),
            'code',
            $normalizer
        );

        $handlers = [
            DeleteProduct::class => new DeleteHandler($client->getProductApi()),
            CreateProductMediaFile::class => new MediaHandler($client->getProductMediaFileApi()),
            CreateProductModelMediaFile::class => new MediaHandler($client->getProductMediaFileApi()),
            ProductFieldInterface::class => $upsertableProductHandler,
            ProductModelFieldInterface::class => $upsertableProductModelHandler,
        ];

        return new Importer(new CommandBus($handlers), new InMemoryQueue());
    }

    public function createByCredentials(string $baseUri, string $clientId, string $secret, string $username, string $password): ImporterInterface
    {
        $clientBuilder = new AkeneoPimClientBuilder($baseUri);
        $client = $clientBuilder->buildAuthenticatedByPassword($clientId, $secret, $username, $password);

        return $this->createByApiClient($client);
    }

    private function createSerializer(array $propertyReplacementMap): NormalizerInterface
    {
        $nameConverter = new CamelCaseToSnakeCaseNameConverter();

        $normalizers = [
            new CommandNormalizer($propertyReplacementMap),
            new DateTimeNormalizer(),
            new DateIntervalNormalizer(),
            new ArrayDenormalizer(),
            new ObjectNormalizer(null, $nameConverter),
        ];

        $serializer = new Serializer($normalizers, []);

        return $serializer;
    }
}
