<?php

namespace Aa\AkeneoImport\CommandHandler\Api\ApiAdapter;

use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\Response;
use Aa\AkeneoImport\ImportCommand\CommandBatchInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UpsertableApiAdapter implements ApiAdapterInterface
{
    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @param UpsertableResourceListInterface $api
     */
    public function send($api, CommandBatchInterface $commands): iterable
    {
        $data = $this->normalizeCommandsToArray($commands);

        $upsertedResources = $api->upsertList($data);

        $responses = [];

        foreach ($upsertedResources as $upsertedResource) {
            $responses[] = new Response($upsertedResource);
        }

        return $responses;
    }

    private function normalizeCommandsToArray(CommandBatchInterface $commands): array
    {
        $data = $this->normalizer->normalize($commands->getItems());

        if (!is_array($data)) {
            throw new CommandHandlerException('Normalizer must return array', $commands->getCommandClass());
        }

        return $data;
    }
}
