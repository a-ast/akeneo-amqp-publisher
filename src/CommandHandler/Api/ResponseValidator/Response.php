<?php

namespace Aa\AkeneoImport\CommandHandler\Api\ResponseValidator;

class Response
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var string
     */
    private $message;

    /**
     * @var array
     */
    private $errors;

    /**
     * @var array
     */
    private $data;

    public function __construct(array $data)
    {
        $this->statusCode = (int)$data['status_code'];
        $this->message = $data['message'] ?? '';
        $this->errors = $data['errors'] ?? [];
        $this->data = $data;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
