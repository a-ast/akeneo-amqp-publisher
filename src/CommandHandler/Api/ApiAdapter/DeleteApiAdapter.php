<?php

namespace Aa\AkeneoImport\CommandHandler\Api\ApiAdapter;

use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\Response;
use Aa\AkeneoImport\ImportCommand\CommandBatchInterface;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Product\DeleteProduct;
use Akeneo\Pim\ApiClient\Api\MediaFileApiInterface;
use Akeneo\Pim\ApiClient\Exception\HttpException;
use ArrayObject;
use Traversable;

class DeleteApiAdapter implements ApiAdapterInterface
{
    /**
     * @param \Akeneo\Pim\ApiClient\Api\Operation\DeletableResourceInterface $api
     */
    public function send($api, CommandBatchInterface $commands): iterable
    {
        $errors = [];

        foreach ($commands->getItems() as $command) {
            try {
                $api->delete($this->getEntityUniqueCode($command));
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

    private function getEntityUniqueCode(CommandInterface $command)
    {
        if ($command instanceof DeleteProduct) {
            return $command->getIdentifier();
        }

        throw new CommandHandlerException('Unsupported class of the delete command.', get_class($command));
    }
}
