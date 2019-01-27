<?php

namespace Aa\AkeneoImport\CommandHandler\Api\Handler;

use Aa\AkeneoImport\CommandBus\CommandPromise;
use Aa\AkeneoImport\ImportCommand\AsyncCommandHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\Exception\RecoverableCommandHandlerException;
use Aa\AkeneoImport\ImportCommand\InitializableCommandHandlerInterface;
use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UpsertableHandler implements AsyncCommandHandlerInterface, InitializableCommandHandlerInterface
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

    /**
     * @var array|CommandPromise[]
     */
    private $promises;

    /**
     * @var \Symfony\Component\Serializer\Normalizer\NormalizerInterface
     */
    private $normalizer;

    public function __construct(UpsertableResourceListInterface $api, string $commandUniqueProperty,
        NormalizerInterface $normalizer, int $batchSize = 100)
    {
        $this->api = $api;
        $this->accumulator = new CommandAccumulator();
        $this->batchSize = $batchSize;
        $this->commandUniqueProperty = $commandUniqueProperty;
        $this->normalizer = $normalizer;
    }

    public function handle(CommandPromise $command)
    {
        $commandData = $this->getNormalizedData($command->getCommand());
        $commandCode = $commandData[$this->commandUniqueProperty];

        if ($this->accumulator->getCountAfterAdding($commandCode) > $this->batchSize) {
            $this->sendCommands();
        }

        $this->accumulator->add($commandCode, $commandData);
        $this->promises[$commandCode] = $command;
    }

    private function sendCommands()
    {
        $commandData = $this->accumulator->getAccumulatedData();

        if (0 === count($commandData)) {
            return;
        }

        $upsertedResources = $this->api->upsertList($commandData);

        // @todo: checks 4XX and that it doesn't clear batches



        foreach ($upsertedResources as $upsertedResource) {

            // gather all failed command codes and return them back with an exception

            // take $codePropertyName

            if (422 === $upsertedResource['status_code']) {

                $code = $upsertedResource[$this->commandUniqueProperty];

                $this->promises[$code]->repeat();

//                throw new RecoverableCommandHandlerException($upsertedResource['message'], 0, [$this->accumulator->getCommand($upsertedResource[$this->commandUniqueProperty])]);
            }
        }

        $this->accumulator->clear();
        $this->promises = [];
    }

    public function setUp()
    {
        // no setup
    }

    public function tearDown()
    {
        $this->sendCommands();
    }

    private function getNormalizedData(CommandInterface $command)
    {
        // @todo: add caching?

        $data = $this->normalizer->normalize($command, 'standard');

        if (false === is_array($data)) {
            throw new CommandHandlerException('Normalizer must returmn array');
        }

        return $data;
    }
}
