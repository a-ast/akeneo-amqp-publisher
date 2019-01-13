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
     * @var ApiRegistry
     */
    private $apiRegistry;

    /**
     * @var ApiAdapterRegistry
     */
    private $apiAdapterRegistry;

    /**
     * @var ValidatorInterface
     */
    private $apiResponseValidator;

    public function __construct(ApiRegistry $apiRegistry,
        ApiAdapterRegistry $apiAdapterRegistry,
        ValidatorInterface $apiResponseValidator
    ) {
        $this->apiRegistry = $apiRegistry;
        $this->apiAdapterRegistry = $apiAdapterRegistry;
        $this->apiResponseValidator = $apiResponseValidator;
    }

    /**
     * @throws CommandHandlerException
     */
    public function handle(CommandBatchInterface $commands)
    {
        $this->validateCommands($commands);

        $commandClass = $commands->getCommandClass();

        $api = $this->apiRegistry->getApi($commandClass);
        $adapter = $this->apiAdapterRegistry->getApiAdapter($commandClass);

        $response = $adapter->send($api, $commands);

        $this->validateResponse($response, $commandClass);

        // @todo
        // 5. Log messages with command list unique id
    }

    public function shouldKeepCommandOrder(): bool
    {
        return true;
    }

    private function validateCommands(CommandBatchInterface $commands)
    {
        if (0 === count($commands)) {
            throw new CommandHandlerException(
                'Number of commands must be greater than zero.', $commands->getCommandClass()
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
