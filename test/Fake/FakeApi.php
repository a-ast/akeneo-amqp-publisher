<?php

namespace Test\Aa\AkeneoImport\Fake;

use Akeneo\Pim\ApiClient\Api\MediaFileApiInterface;
use Akeneo\Pim\ApiClient\Api\Operation\DeletableResourceInterface;
use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;

class FakeApi implements UpsertableResourceListInterface, DeletableResourceInterface, MediaFileApiInterface
{
    private $log = [];

    private $responses = [];

    public function getRequestLog()
    {
        return $this->log;
    }

    public function upsertList($resources)
    {
        $this->log[] = $resources;

        $response = [];

        foreach ($resources as $resource) {

            $entityCodeFieldName = isset($resource['identifier']) ? 'identifier' : 'code';
            $entityCode = $resource[$entityCodeFieldName];

            if (!isset($this->responses[$entityCode])) {
                continue;
            }

            if ($this->responses[$entityCode]['times'] < 1) {
                continue;
            }

            $response[] = [
                $entityCodeFieldName => $entityCode,
                'status_code' => $this->responses[$entityCode]['status_code'],
                'message' => $this->responses[$entityCode]['message'],
            ];

            $this->responses[$entityCode]['times']--;
        }

        return $response;
    }

    public function addUpsertResponse(string $fieldName, string $entityCode, int $statusCode, string $message, int $times)
    {
        $this->responses[$entityCode] = [
            'field' => $fieldName,
            'status_code' => $statusCode,
            'message' => $message,
            'times' => $times,
        ];
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
