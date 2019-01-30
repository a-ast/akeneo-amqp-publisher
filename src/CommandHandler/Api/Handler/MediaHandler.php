<?php

namespace Aa\AkeneoImport\CommandHandler\Api\Handler;

use Aa\AkeneoImport\CommandHandler\Api\ResponseHandler;
use Aa\AkeneoImport\ImportCommand\CommandCallbacks;
use Aa\AkeneoImport\ImportCommand\CommandHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Media\CreateProductMediaFile;
use Aa\AkeneoImport\ImportCommand\Media\CreateProductModelMediaFile;
use Akeneo\Pim\ApiClient\Api\MediaFileApiInterface;
use Akeneo\Pim\ApiClient\Exception\UnprocessableEntityHttpException;

class MediaHandler implements CommandHandlerInterface
{
    /**
     * @var MediaFileApiInterface
     */
    private $api;

    /**
     * @var \Aa\AkeneoImport\CommandHandler\Api\ResponseHandler
     */
    private $responseHandler;

    public function __construct(MediaFileApiInterface $api, ResponseHandler $responseHandler)
    {
        $this->api = $api;
        $this->responseHandler = $responseHandler;
    }

    public function handle(CommandInterface $command, CommandCallbacks $callbacks = null)
    {
        if (!$command instanceof CreateProductMediaFile && !$command instanceof CreateProductModelMediaFile) {
            return;
        }

        $meta = $this->getCommandMetadata($command);
        try {

            $this->api->create($command->getFileName(), $meta);

        } catch (UnprocessableEntityHttpException $e) {

            $this->responseHandler->handle($command, $e->getCode(), $e->getMessage(), $callbacks);

        }
    }

    private function getCommandMetadata(CommandInterface $command): array
    {
        if ($command instanceof CreateProductMediaFile) {
            return [
                'identifier' => $command->getProductIdentifier(),
                'attribute' => $command->getAttributeCode(),
                'scope' => $command->getScope(),
                'locale' => $command->getLocale(),
                'type' => 'product',
            ];
        }

        if ($command instanceof CreateProductModelMediaFile) {
            return [
                'code' => $command->getProductModelCode(),
                'attribute' => $command->getAttributeCode(),
                'scope' => $command->getScope(),
                'locale' => $command->getLocale(),
                'type' => 'product_model',
            ];
        }

        throw new CommandHandlerException('Unsupported class of the create media command.', $command);
    }
}
