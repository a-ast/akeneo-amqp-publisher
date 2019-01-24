<?php

namespace Test\Aa\AkeneoImport;

use Aa\AkeneoImport\Import\ApiImporterFactory;
use Aa\AkeneoImport\Import\ImporterInterface;
use Aa\AkeneoImport\ImportCommand;
use Aa\ArrayDiff\Calculator;
use Aa\ArrayDiff\Matcher\SimpleMatcher;
use PHPUnit\Framework\TestCase;
use Test\Aa\AkeneoImport\Fake\FakeApiClient;

class ExampleTest extends TestCase
{

    /**
     * @var FakeApiClient
     */
    private $client;

    /**
     * @var ImporterInterface
     */
    private $importer;

    protected function setUp()
    {
        $this->client = new FakeApiClient();

        $factory = new ApiImporterFactory();
        $this->importer = $factory->createByApiClient($this->client);
    }

    public function test_import_product_builder()
    {
        $commandBuilder = new ImportCommand\Product\ProductCommandBuilder('1');
        $commandBuilder
            ->setFamily('t-shirt')
            ->setCategories(['clothing'])
            ->setEnabled(true)
            ->addValue('color', 'red', 'en_EN')
            ->addValue('size', 'M', null, 'web')
        ;

        $this->importer->import($commandBuilder->getCommands());

        $upsertData = [
            'identifier' => '1',
            'categories' => ['clothing'],
            'family' => 't-shirt',
            'enabled' => true,
            'values' => [
                'color' => [
                    ['data' => 'red', 'locale' => 'en_EN', 'scope' => null]
                ],
                'size' => [
                    ['data' => 'M', 'locale' => null, 'scope' => 'web']
                ]
            ]
        ];

        $expected = [
            ['api' => 'product', $upsertData],
        ];

        $requestLog = $this->client->getRequestLog();

        $this->assertArraysAreEqual($requestLog, $expected);
    }

    private function assertArraysAreEqual(array $actual, array $expected): void
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
