<?php

namespace spec\Aa\AkeneoImport\CommandHandler\Api\Handler\fixture;

use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Product\ProductFieldInterface;

class TestCommand implements CommandInterface, ProductFieldInterface
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var array
     */
    private $attributes;

    public function __construct(string $identifier, array $attributes = [])
    {
        $this->identifier = $identifier;
        $this->attributes = $attributes;
    }

    public function getProductIdentifier(): string
    {
        return $this->identifier;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
