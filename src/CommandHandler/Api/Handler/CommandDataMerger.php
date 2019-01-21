<?php

namespace Aa\AkeneoImport\CommandHandler\Api\Handler;

class CommandDataMerger
{
    public function merge(array $data, string $groupKey): array
    {
        $grouped = [];

        foreach ($data as $item) {

            $id = $item[$groupKey];

            $grouped[$id] = array_merge($grouped[$id] ?? [], $item);
        }

        return array_values($grouped);
    }
}
