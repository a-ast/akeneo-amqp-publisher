<?php

namespace Aa\AkeneoImport\CommandHandler\Api;

use Aa\AkeneoImport\CommandHandler\Api\ApiAdapter\ApiAdapterInterface;
use Aa\AkeneoImport\ImportCommand\Category\UpdateOrCreateCategory;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Media\CreateProductMediaFile;
use Aa\AkeneoImport\ImportCommand\Media\CreateProductModelMediaFile;
use Aa\AkeneoImport\ImportCommand\Product\DeleteProduct;
use Aa\AkeneoImport\ImportCommand\Product\UpdateOrCreateProduct;
use Aa\AkeneoImport\ImportCommand\ProductModel\UpdateOrCreateProductModel;

class ApiAdapterRegistry
{
    /**
     * @var array
     */
    private $adapters;

    public function __construct(array $adapters)
    {
        $this->adapters = $adapters;
    }

    public function getApiAdapter(string $commandClass): ApiAdapterInterface
    {
        switch ($commandClass) {
            case UpdateOrCreateProduct::class:
            case UpdateOrCreateCategory::class:
            case UpdateOrCreateProductModel::class:
                return $this->adapters['upsert'];

            case CreateProductMediaFile::class:
            case CreateProductModelMediaFile::class:
                return $this->adapters['media'];

            case DeleteProduct::class:
                return $this->adapters['delete'];

            default:
                throw new CommandHandlerException(
                    sprintf(
                        'An API adapter for the class %s not found.',
                        $commandClass
                    ), $commandClass
                );
        }
    }
}
