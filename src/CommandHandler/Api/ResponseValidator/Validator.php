<?php

namespace Aa\AkeneoImport\CommandHandler\Api\ResponseValidator;

use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\Exception\TolerantValidationException;
use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\Exception\ValidationException;
use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;
use Traversable;

class Validator implements ValidatorInterface
{
    const UNPROCESSABLE_ENTITY = 422;
    const BAD_REQUEST = 400;

    /**
     * @param \Traversable|Response[] $responses
     */
    public function validate(Traversable $responses)
    {
        // Check tolerant cases first
        foreach ($responses as $response) {
            $this->checkMissingProductParent($response);
        }

        // @todo: combine errors to one for logging to know what entities failed?
        foreach ($responses as $response) {

            if (self::BAD_REQUEST <= $response->getStatusCode()) {
                throw new ValidationException($response->getMessage(), $response);
            }
        }
    }

    private function checkMissingProductParent(Response $response)
    {
        if (self::UNPROCESSABLE_ENTITY === $response->getStatusCode() &&
            false !== strpos(
                $response->getMessage(),
                'Property "parent" expects a valid parent code.'
            )) {

            throw new TolerantValidationException($response->getMessage(), $response);
        }
    }

    public function supportsApi($api, string $commandClass)
    {
        return $api instanceof UpsertableResourceListInterface;
    }
}
