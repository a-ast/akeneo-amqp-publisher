<?php

namespace Aa\AkeneoImport\CommandHandler\Api\Handler;

use Aa\AkeneoImport\ImportCommand\CommandHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Media\CreateProductMediaFile;
use Aa\AkeneoImport\ImportCommand\Media\CreateProductModelMediaFile;
use Akeneo\Pim\ApiClient\Api\MediaFileApiInterface;

class MediaHandler implements CommandHandlerInterface
{
    /**
     * @var MediaFileApiInterface
     */
    private $api;

    public function __construct(MediaFileApiInterface $api)
    {
        $this->api = $api;
    }

    public function handle(CommandInterface $command)
    {
        if (!$command instanceof CreateProductMediaFile && !$command instanceof CreateProductModelMediaFile) {
            return;
        }

        $meta = $this->getCommandMetadata($command);
        $this->api->create($command->getFileName(), $meta);
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

        throw new CommandHandlerException('Unsupported class of the create media command.');
    }
}
