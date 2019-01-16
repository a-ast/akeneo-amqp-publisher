<?php

namespace Aa\AkeneoImport\CommandHandler\Api;

use Aa\AkeneoImport\ImportCommand\CommandTypes;
use Aa\AkeneoImport\ImportCommand\Category\UpdateOrCreateCategory;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Media\CreateProductMediaFile;
use Aa\AkeneoImport\ImportCommand\Media\CreateProductModelMediaFile;
use Aa\AkeneoImport\ImportCommand\Product\UpdateOrCreateProduct;
use Aa\AkeneoImport\ImportCommand\Product\DeleteProduct;
use Aa\AkeneoImport\ImportCommand\ProductModel\UpdateOrCreateProductModel;
use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;

class ApiRegistry
{

    /**
     * @var AkeneoPimClientInterface
     */
    private $client;

    public function __construct(AkeneoPimClientInterface $client)
    {
        $this->client = $client;
    }

    public function getApi(string $commandClass)
    {
        switch ($commandClass) {
            case CommandTypes::PRODUCT:
            case UpdateOrCreateProduct::class:
            case DeleteProduct::class:
                return $this->client->getProductApi();

            case UpdateOrCreateProductModel::class:
                return $this->client->getProductModelApi();

            case UpdateOrCreateCategory::class:
                return $this->client->getCategoryApi();

            case CreateProductMediaFile::class:
            case CreateProductModelMediaFile::class:
                return $this->client->getProductMediaFileApi();

            default:
                // @todo: return null for non implemented commands, log and skip?
                throw new CommandHandlerException(
                    sprintf(
                        'An Akeneo API for the class %s not found.',
                        $commandClass
                    ), $commandClass
                );
        }
    }
}
