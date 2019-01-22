<?php

namespace Aa\AkeneoImport\CommandHandler\Api\Handler;

use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CommandAccumulator
{
    /**
     * @var array|CommandInterface[]
     */
    private $commands = [];

    /**
     * @var array[]
     */
    private $normalizedData = [];

    /**
     * @var array|string[]
     */
    private $addedCommandCodes = [];

    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @var string
     */
    private $commandUniqueProperty;

    public function __construct(NormalizerInterface $normalizer, string $commandUniqueProperty)
    {
        $this->normalizer = $normalizer;
        $this->commandUniqueProperty = $commandUniqueProperty;
    }

    public function add(CommandInterface $command)
    {
        $commandData = $this->getNormalizedData($command);
        $commandCode = $commandData[$this->commandUniqueProperty];

        $this->commands[$commandCode] = $command;
        $this->normalizedData[$commandCode] = array_merge($this->normalizedData[$commandCode] ?? [], $commandData);

        $this->addedCommandCodes = $this->getUniqueCodesAfterAdding($commandCode);
    }

    public function getCountAfterAdding(CommandInterface $command): int
    {
        $commandData = $this->getNormalizedData($command);
        $commandCode = $commandData[$this->commandUniqueProperty];

        return count($this->getUniqueCodesAfterAdding($commandCode));
    }

    public function getAccumulatedData(): array
    {
        return $this->normalizedData;
    }

    public function getCommands(): iterable
    {
        return $this->commands;
    }

    public function clear(): void
    {
        $this->commands = [];
        $this->normalizedData = [];
        $this->addedCommandCodes = [];
    }

    private function getUniqueCodesAfterAdding(string $code): array
    {
        return array_unique(array_merge($this->addedCommandCodes, [$code]));
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
