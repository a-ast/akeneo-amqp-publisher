<?php

namespace Aa\AkeneoImport\CommandHandler\Api;

use Aa\AkeneoImport\CommandHandler\Api\ApiAdapter\MediaApiAdapter;
use Aa\AkeneoImport\CommandHandler\Api\ApiAdapter\UpsertableApiAdapter;
use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\Validator;
use Aa\AkeneoImport\Normalizer\CommandBatchNormalizer;
use Aa\AkeneoImport\Normalizer\CommandNormalizer;
use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateIntervalNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiBatchHandlerFactory
{

    /**
     * @var AkeneoPimClientInterface
     */
    private $client;

    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    public function __construct(AkeneoPimClientInterface $client)
    {
        $this->client = $client;
        $this->normalizer = $this->createSerializer();
    }

    public function createHandler()
    {
        $apiAdapters = [
            new UpsertableApiAdapter(),
            new MediaApiAdapter(),
        ];

        $validator = new Validator();

        return new ApiBatchHandler($this->client, $this->normalizer, $apiAdapters, $validator);
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
