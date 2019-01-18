<?php

namespace Aa\AkeneoImport\CommandHandler\Api\Handler;

use Aa\AkeneoImport\ImportCommand\CommandHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Control\FinishImport;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Product\ProductFieldInterface;
use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UpsertableHandler implements CommandHandlerInterface
{
    /**
     * @var UpsertableResourceListInterface
     */
    private $api;

    /**
     * @var NormalizerInterface
     */
    private $normalizer;


    /**
     * @var CommandAccumulator
     */
    private $accumulator;

    public function __construct(UpsertableResourceListInterface $api, NormalizerInterface $normalizer, int $batchSize = 100)
    {
        $this->api = $api;
        $this->normalizer = $normalizer;
        $this->accumulator = new CommandAccumulator($batchSize);
    }

    public function handle(CommandInterface $command)
    {
        if ($command instanceof FinishImport) {
            $this->sendCommands($this->accumulator->getCommands());

            return;
        }

        $commandCode = $this->getEntityUniqueCode($command);

        if ($this->accumulator->isFullAfter($commandCode)) {
            $this->sendCommands($this->accumulator->getCommands());
        }

        $this->accumulator->add($commandCode, $command);
    }

    private function sendCommands(iterable $commands)
    {
        if (0 === count($commands)) {
            return;
        }

        $commandData = $this->normalizer->normalize($commands);

        if (!is_array($commandData)) {
            throw new CommandHandlerException('Normalizer must return an array');
        }

        $upsertedResources = $this->api->upsertList($commandData);

        // @todo: checks 4XX and that it doesn't clear batches

        $this->accumulator->clear();

        foreach ($upsertedResources as $upsertedResource) {
            if (422 === $upsertedResource['status_code']) {
                throw new CommandHandlerException($upsertedResource['message']);
            }
        }
    }

    private function getEntityUniqueCode(CommandInterface $command)
    {
        if ($command instanceof ProductFieldInterface) {
            return $command->getProductIdentifier();
        }

        return $command->getProductModelCode();
    }
}
