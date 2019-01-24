<?php

namespace Aa\AkeneoImport\CommandHandler\Api\Handler;

use Aa\AkeneoImport\CommandHandler\Api\CommandClassHelper;
use Aa\AkeneoImport\ImportCommand\CommandHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Control\FinishImport;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\InitializableCommandHandlerInterface;
use Aa\AkeneoImport\ImportCommand\Product\ProductFieldInterface;
use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UpsertableHandler implements InitializableCommandHandlerInterface
{
    /**
     * @var UpsertableResourceListInterface
     */
    private $api;

    /**
     * @var CommandAccumulator
     */
    private $accumulator;

    /**
     * @var int
     */
    private $batchSize;

    /**
     * @var string
     */
    private $commandUniqueProperty;

    public function __construct(UpsertableResourceListInterface $api, string $commandUniqueProperty, NormalizerInterface $normalizer, int $batchSize = 100)
    {
        $this->api = $api;
        $this->accumulator = new CommandAccumulator($normalizer, $commandUniqueProperty);
        $this->batchSize = $batchSize;
        $this->commandUniqueProperty = $commandUniqueProperty;
    }

    public function handle(CommandInterface $command)
    {
        if ($this->accumulator->getCountAfterAdding($command) > $this->batchSize) {
            $this->sendCommands();
        }

        $this->accumulator->add($command);
    }

    private function sendCommands()
    {
        $commandData = $this->accumulator->getAccumulatedData();

        if (0 === count($commandData)) {
            return;
        }

        $upsertedResources = $this->api->upsertList($commandData);

        // @todo: checks 4XX and that it doesn't clear batches

        $this->accumulator->clear();

        foreach ($upsertedResources as $upsertedResource) {

            // gather all failed command codes and return them back with an exception

            // take $codePropertyName


            if (422 === $upsertedResource['status_code']) {
                throw new CommandHandlerException($upsertedResource['message']);
            }
        }
    }

    public function setUp()
    {
        // no setup
    }

    public function tearDown()
    {
        $this->sendCommands();
    }
}
