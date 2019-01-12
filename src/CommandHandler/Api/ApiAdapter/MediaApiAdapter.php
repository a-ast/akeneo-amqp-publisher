<?php

namespace Aa\AkeneoImport\CommandHandler\Api\ApiAdapter;

use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\Response;
use Aa\AkeneoImport\ImportCommand\CommandBatchInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Media\CreateProductMediaFile;
use Aa\AkeneoImport\ImportCommand\Media\CreateProductModelMediaFile;
use Akeneo\Pim\ApiClient\Api\MediaFileApiInterface;
use Akeneo\Pim\ApiClient\Exception\HttpException;

class MediaApiAdapter implements ApiAdapterInterface
{
    /**
     * @param MediaFileApiInterface $api
     */
    public function send($api, CommandBatchInterface $commands): iterable
    {
        $errors = [];

        foreach ($commands->getItems() as $command) {
            try {

                $meta = $this->getCommandMetadata($command);
                $api->create($command->getFileName(), $meta);

            } catch (HttpException $e) {

                $errors[] = new Response([
                    'status_code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'data' => $command,
                ]);
            }
        }

        return $errors;
    }

    private function getCommandMetadata($command): array
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
                'type' => 'product',
            ];
        }

        throw new CommandHandlerException('Unsupported class of the create media command.', get_class($command));
    }
}
