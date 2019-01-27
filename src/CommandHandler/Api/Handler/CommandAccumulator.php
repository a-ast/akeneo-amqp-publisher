<?php

namespace Aa\AkeneoImport\CommandHandler\Api\Handler;

class CommandAccumulator
{
    /**
     * @var array[]
     */
    private $normalizedData = [];

    /**
     * @var array|string[]
     */
    private $addedCommandCodes = [];


    public function add(string $commandCode, array $commandData)
    {
        $this->normalizedData[$commandCode] = array_merge($this->normalizedData[$commandCode] ?? [], $commandData);

        $this->addedCommandCodes = $this->getUniqueCodesAfterAdding($commandCode);
    }

    public function getCountAfterAdding(string $commandCode): int
    {
        return count($this->getUniqueCodesAfterAdding($commandCode));
    }

    public function getAccumulatedData(): array
    {
        return $this->normalizedData;
    }

    public function clear(): void
    {
        $this->normalizedData = [];
        $this->addedCommandCodes = [];
    }

    private function getUniqueCodesAfterAdding(string $code): array
    {
        return array_unique(array_merge($this->addedCommandCodes, [$code]));
    }

}
