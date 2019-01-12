<?php

namespace Aa\AkeneoImport\CommandHandler\Api;

use Aa\AkeneoImport\CommandHandler\Api\ApiAdapter\ApiAdapterInterface;
use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\Exception\TolerantValidationException;
use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\Response;
use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\ValidatorInterface;
use Aa\AkeneoImport\ImportCommand\Category\UpdateOrCreateCategory;
use Aa\AkeneoImport\ImportCommand\CommandHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandBatchInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Exception\RecoverableCommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Media\CreateProductMediaFile;
use Aa\AkeneoImport\ImportCommand\Media\CreateProductModelMediaFile;
use Aa\AkeneoImport\ImportCommand\Product\DeleteProduct;
use Aa\AkeneoImport\ImportCommand\Product\UpdateOrCreateProduct;
use Aa\AkeneoImport\ImportCommand\ProductModel\UpdateOrCreateProductModel;
use Akeneo\Pim\ApiClient\AkeneoPimClientInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Throwable;


class ApiCommandHandler implements CommandHandlerInterface
{
    /**
     * @var AkeneoPimClientInterface
     */
    private $client;

    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @var ApiAdapterInterface[]
     */
    private $apiAdapters;

    /**
     * @var ValidatorInterface
     */
    private $apiResponseValidator;

    public function __construct(AkeneoPimClientInterface $client,
        NormalizerInterface $normalizer,
        array $apiAdapters,
        ValidatorInterface $apiResponseValidator
    ) {
        $this->client = $client;
        $this->normalizer = $normalizer;
        $this->apiAdapters = $apiAdapters;
        $this->apiResponseValidator = $apiResponseValidator;
    }

    /**
     * @throws CommandHandlerException
     */
    public function handle(CommandBatchInterface $commands)
    {
        $commandClass = $commands->getCommandClass();

        if (0 === count($commands)) {
            throw new CommandHandlerException('Number of commands must be greater than zero.', $commandClass);
        }

        $api = $this->getApi($commandClass);

        $adapter = $this->findAdapter($commandClass);
        $response = $adapter->send($api, $commands);

        $this->validateResponse($response, $commandClass);

        // @todo
        // 5. Log messages with command list unique id
    }

    public function shouldKeepCommandOrder(): bool
    {
        return true;
    }

    private function getApi(string $commandClass)
    {
        switch ($commandClass) {
            case UpdateOrCreateProduct::class:
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

    private function findAdapter(string $commandClass): ApiAdapterInterface
    {
        switch ($commandClass) {
            case UpdateOrCreateProduct::class:
            case UpdateOrCreateCategory::class:
            case UpdateOrCreateProductModel::class:
                return $this->apiAdapters['upsertable'];

            case CreateProductMediaFile::class:
            case CreateProductModelMediaFile::class:
                return $this->apiAdapters['media'];

            case DeleteProduct::class:
                return $this->apiAdapters['delete'];

            default:
                throw new CommandHandlerException(
                    sprintf(
                        'An API adapter for the class %s not found.',
                        $commandClass
                    ), $commandClass
                );
        }
    }

    /**
     * @param iterable|Response[] $response
     */
    private function validateResponse(iterable $response, string $commandClass): void
    {
        try {
            $this->apiResponseValidator->validate($response);
        } catch (TolerantValidationException $e) {
            throw new RecoverableCommandHandlerException($e->getMessage(), $commandClass, $e);
        } catch (Throwable $e) {
            throw new CommandHandlerException($e->getMessage(), $commandClass, $e);
        }
    }
}
