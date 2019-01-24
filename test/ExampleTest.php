<?php

namespace Test\Aa\AkeneoImport;

use Aa\AkeneoImport\Import\ApiImporterFactory;
use Aa\AkeneoImport\ImportCommand;
use Aa\ArrayDiff\Calculator;
use Aa\ArrayDiff\Matcher\SimpleMatcher;
use PHPUnit\Framework\TestCase;
use Test\Aa\AkeneoImport\Fake\FakeApiClient;

class ExampleTest extends TestCase
{
    public function test_import_product_builder()
    {
        $client = new FakeApiClient();

        $factory = new ApiImporterFactory();
        $importer = $factory->createByApiClient($client);

        $commandBuilder = new ImportCommand\Product\ProductCommandBuilder('1');
        $commandBuilder
            ->setFamily('t-shirt')
            ->setEnabled(true);

        $importer->import($commandBuilder->getCommands());

        $expected = [
            ['api' => 'product', ['identifier' => '1', 'family' => 't-shirt', 'enabled' => true]],
        ];

        $requestLog = $client->getRequestLog();

        $this->aseertArraysAreEqual($requestLog, $expected);
    }

    private function aseertArraysAreEqual(array $actual, array $expected): void
    {
        $diffCalc = new Calculator(new SimpleMatcher());
        $diff = $diffCalc->calculateDiff($actual, $expected);

        if (count($diff->getMissing()) + count($diff->getMissing()) > 0) {
            print PHP_EOL.$diff->toString();
        }

        $this->assertEmpty($diff->getMissing());
        $this->assertEmpty($diff->getUnmatched());
    }
}
