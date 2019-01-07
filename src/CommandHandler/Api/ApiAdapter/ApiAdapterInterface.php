<?php

namespace Aa\AkeneoImport\CommandHandler\Api\ApiAdapter;

use Traversable;

interface ApiAdapterInterface
{
    public function send($api, array $data): Traversable;

    public function supportsApi($api);
}
