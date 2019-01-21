<?php

namespace Aa\AkeneoImport\CommandHandler\Api\Handler;

class CommandDataMerger
{
    public function merge(array $data, string $groupKey, string $replacementKey): array
    {
        $grouped = [];

        foreach ($data as $item) {

            $id = $item[$groupKey];

            $grouped[$id] = array_merge($grouped[$id] ?? [], $item);
        }


        foreach ($grouped as &$item) {

            $item[$replacementKey] = $item[$groupKey];
            unset($item[$groupKey]);
        }

        return array_values($grouped);
    }
}
