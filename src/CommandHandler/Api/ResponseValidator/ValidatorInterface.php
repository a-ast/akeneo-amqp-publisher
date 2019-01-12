<?php

namespace Aa\AkeneoImport\CommandHandler\Api\ResponseValidator;

use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\Response;

interface ValidatorInterface
{

    /**
     * @param iterable|Response[] $responses
     */
    public function validate(iterable $responses);

    public function supportsApi($api, string $commandClass);
}
