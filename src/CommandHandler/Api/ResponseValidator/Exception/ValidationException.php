<?php

namespace Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\Exception;

use Aa\AkeneoImport\CommandHandler\Api\ResponseValidator\Response;
use RuntimeException;
use Throwable;

class ValidationException extends RuntimeException
{
    /**
     * @var Response
     */
    private $response;

    public function __construct(string $message, Response $response, Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);

        $this->response = $response;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
