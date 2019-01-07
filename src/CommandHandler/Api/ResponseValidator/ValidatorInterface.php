<?php

namespace Aa\AkeneoImport\CommandHandler\Api\ResponseValidator;

use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\Response;
use Traversable;

interface ValidatorInterface
{

    /**
     * @param \Traversable|Response[] $responses
     */
    public function validate(Traversable $responses);

    public function supportsApi($api, string $commandClass);
}
