<?php

namespace Aa\AkeneoImport\CommandHandler\Api\ApiAdapter;

use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\Response;
use Aa\AkeneoImport\ImportCommand\CommandBatchInterface;

interface ApiAdapterInterface
{
    /**
     * @return Response[]
     */
    public function send($api, CommandBatchInterface $commands): iterable;
}
