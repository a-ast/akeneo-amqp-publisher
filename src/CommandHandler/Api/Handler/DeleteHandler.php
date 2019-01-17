<?php

namespace Aa\AkeneoImport\CommandHandler\Api\Handler;

use Aa\AkeneoImport\ImportCommand\CommandHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Product\DeleteProduct;
use Akeneo\Pim\ApiClient\Api\Operation\DeletableResourceInterface;

class DeleteHandler implements CommandHandlerInterface
{
    /**
     * @var \Akeneo\Pim\ApiClient\Api\Operation\DeletableResourceInterface
     */
    private $api;

    public function __construct(DeletableResourceInterface $api)
    {
        $this->api = $api;
    }

    public function handle(CommandInterface $command)
    {
        $this->api->delete($this->getEntityUniqueCode($command));
    }

    private function getEntityUniqueCode(CommandInterface $command)
    {
        if ($command instanceof DeleteProduct) {
            return $command->getIdentifier();
        }

        throw new CommandHandlerException('Unsupported class of the delete command.');
    }
}
