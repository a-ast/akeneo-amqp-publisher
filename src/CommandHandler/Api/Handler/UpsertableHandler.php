<?php

namespace Aa\AkeneoImport\CommandHandler\Api\Handler;

use Aa\AkeneoImport\CommandBus\CommandPromise;
use Aa\AkeneoImport\ImportCommand\CommandCallbacks;
use Aa\AkeneoImport\ImportCommand\CommandHandlerInterface;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Aa\AkeneoImport\ImportCommand\InitializableCommandHandlerInterface;
use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UpsertableHandler implements CommandHandlerInterface, InitializableCommandHandlerInterface
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
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @var array|CommandInterface[]
     */
    private $commands;

    /**
     * @var array|CommandCallbacks[]|null[]
     */
    private $commandCallbacks;

    public function __construct(UpsertableResourceListInterface $api, string $commandUniqueProperty,
        NormalizerInterface $normalizer, int $batchSize = 100)
    {
        $this->api = $api;
        $this->accumulator = new CommandAccumulator();
        $this->batchSize = $batchSize;
        $this->commandUniqueProperty = $commandUniqueProperty;
        $this->normalizer = $normalizer;
    }

    public function handle(CommandInterface $command, CommandCallbacks $callbacks = null)
    {
        $commandData = $this->getNormalizedData($command);
        $commandCode = $commandData[$this->commandUniqueProperty];

        if ($this->accumulator->getCountAfterAdding($commandCode) > $this->batchSize) {
            $this->sendCommands();
        }

        $this->accumulator->add($commandCode, $commandData);
        $this->commands[$commandCode] = $command;
        $this->commandCallbacks[$commandCode] = $callbacks;
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

                $callBacks = $this->commandCallbacks[$code];

                if (null === $callBacks) {
                    continue;
                }

                $command = $this->commands[$code];
                $callBacks->repeat($command);

//                throw new RecoverableCommandHandlerException($upsertedResource['message'], 0, [$this->accumulator->getCommand($upsertedResource[$this->commandUniqueProperty])]);
            }
        }

        $this->accumulator->clear();
        $this->commands = [];
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
