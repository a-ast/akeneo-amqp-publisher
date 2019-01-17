<?php

namespace Aa\AkeneoImport\CommandHandler\Api\Handler;

use Aa\AkeneoImport\ImportCommand\CommandHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Control\FinishImport;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UpsertableHandler implements CommandHandlerInterface
{
    /**
     * @var UpsertableResourceListInterface
     */
    private $api;

    /**
     * @var \Symfony\Component\Serializer\Normalizer\NormalizerInterface
     */
    private $normalizer;

    /**
     * @var array
     */
    private $accumulatedCommands = [];

    /**
     * @var int
     */
    private $batchSize;

    public function __construct(UpsertableResourceListInterface $api, NormalizerInterface $normalizer, int $batchSize = 100)
    {
        $this->api = $api;
        $this->normalizer = $normalizer;
        $this->batchSize = $batchSize;
    }

    public function handle(CommandInterface $command)
    {
        if ($command instanceof FinishImport) {
            $this->sendAll();

            return;
        }

        if (count($this->accumulatedCommands) === $this->batchSize) {

            $this->sendAll();

            $this->accumulatedCommands = [$command];

            return;
        }

        $this->accumulatedCommands[] = $command;
    }

    private function sendAll()
    {
        $commandData = $this->normalizer->normalize($this->accumulatedCommands);

        if (!is_array($commandData)) {
            throw new CommandHandlerException('Normalizer must return an array');
        }

        $upsertedResources = $this->api->upsertList($commandData);

        foreach ($upsertedResources as $upsertedResource) {
            if (422 === $upsertedResource['status_code']) {
                throw new CommandHandlerException($upsertedResource['message']);
            }
        }
    }
}
