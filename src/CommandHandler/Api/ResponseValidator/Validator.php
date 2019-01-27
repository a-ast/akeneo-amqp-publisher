<?php

namespace Aa\AkeneoImport\CommandHandler\Api\ResponseValidator;

use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\Exception\TolerantValidationException;
use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\Exception\ValidationException;
use Akeneo\Pim\ApiClient\Api\Operation\UpsertableResourceListInterface;

class Validator implements ValidatorInterface
{
    const UNPROCESSABLE_ENTITY = 422;
    const BAD_REQUEST = 400;

    /**
     * @param iterable|Response[] $responses
     */
    public function validate(iterable $responses)
    {

        $entityCodes = [];
        // Check recoverable cases first
        foreach ($responses as $response) {
            if ($this->isRecoverable($response)) {
                $entityCodes[] = $response->getEntityCode();
            };
        }

        // @todo: combine errors to one for logging to know what entities failed?
        foreach ($responses as $response) {

            if (self::BAD_REQUEST <= $response->getStatusCode()) {
                throw new ValidationException($response->getMessage(), $response);
            }
        }
    }

    private function isRecoverable(Response $response): bool
    {
        return (self::UNPROCESSABLE_ENTITY === $response->getStatusCode() &&
            false !== strpos(
                $response->getMessage(),
                'Property "parent" expects a valid parent code.'
            ));
    }
}
