<?php

namespace Test\Aa\AkeneoImport\Fake;

use Akeneo\Pim\ApiClient\Api\MediaFileApiInterface;
use Akeneo\Pim\ApiClient\Api\Operation\DeletableResourceInterface;
use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;

class FakeApi implements UpsertableResourceListInterface, DeletableResourceInterface, MediaFileApiInterface
{
    private $log = [];

    public function getRequestLog()
    {
        return $this->log;
    }

    public function upsertList($resources)
    {
        $this->log[] = $resources;

        return [];
    }

    public function delete($code)
    {
        $this->log[] = $code;
    }

    public function create($mediaFile, array $data)
    {
        $this->log[] = [
            $mediaFile,
            $data,
        ];
    }

    public function download($code) {}

    public function get($code) {}

    public function listPerPage($limit = 10, $withCount = false, array $queryParameters = []) {}

    public function all($pageSize = 10, array $queryParameters = []) {}
}
