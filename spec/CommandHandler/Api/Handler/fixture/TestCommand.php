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

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public function getProductIdentifier(): string
    {
        return $this->identifier;
    }
}
