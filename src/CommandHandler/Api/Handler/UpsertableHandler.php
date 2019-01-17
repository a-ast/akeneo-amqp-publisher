<?php

namespace Aa\AkeneoImport\CommandHandler\Api\Handler;

use Aa\AkeneoImport\ImportCommand\CommandHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Control\FinishImport;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Media\CreateProductMediaFile;
use Aa\AkeneoImport\ImportCommand\Media\CreateProductModelMediaFile;
use Akeneo\Pim\ApiClient\Api\MediaFileApiInterface;
use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UpsertableHandler implements CommandHandlerInterface
{
    /**
     * @var MediaFileApiInterface
     */
    private $api;

    /**
     * @var \Symfony\Component\Serializer\Normalizer\NormalizerInterface
     */
    private $normalizer;

    public function __construct(UpsertableResourceListInterface $api, NormalizerInterface $normalizer)
    {
        $this->api = $api;
        $this->normalizer = $normalizer;
    }

    public function handle(CommandInterface $command)
    {
        if ($command instanceof FinishImport) {
            $this->sendAll();

            return;
        }

        // @todo: ignore `type` using AbstractNormalizer::IGNORED_ATTRIBUTES
        $commandData = $this->normalizer->normalize($command);
        unset($commandData['type']);

        $entityCode = $commandData['productIdentifier'];

//        $data[$entityCode] = array_merge($data[$entityCode] ?? [], $commandData);

        // @todo: chunk for 100 lines
        // $upsertedResources = $this->api->upsertList([$commandData]);

    }


    private function sendAll()
    {
        // @todo:
    }
}
