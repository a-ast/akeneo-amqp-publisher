<?php

namespace Test\Aa\AkeneoImport;

use Aa\AkeneoImport\Import\ApiImporterFactory;
use Aa\AkeneoImport\Import\ImporterInterface;
use Aa\AkeneoImport\ImportCommand;
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
        $this->importer = $factory->createByApiClient($this->client, 3);
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
            'family' => 't-shirt',
            'categories' => ['clothing'],
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

        $this->assertSame($requestLog, $expected);
    }

    public function test_import_products_in_batch()
    {
        for ($i = 1; $i <= 10; $i++) {
            $commands[] = new ImportCommand\Product\Create($i);
        }

        $this->importer->import($commands);

        $expected = [
            [
                'api' => 'product',
                ['identifier' => '1'],
                ['identifier' => '2'],
                ['identifier' => '3'],
            ],
            [
                'api' => 'product',
                ['identifier' => '4'],
                ['identifier' => '5'],
                ['identifier' => '6'],
            ],
            [
                'api' => 'product',
                ['identifier' => '7'],
                ['identifier' => '8'],
                ['identifier' => '9'],
            ],
            [
                'api' => 'product',
                ['identifier' => '10'],
            ],
        ];

        $requestLog = $this->client->getRequestLog();

        $this->assertSame($requestLog, $expected);
    }

    public function test_that_products_are_republished()
    {
        $commands = [
            new ImportCommand\Product\Create('1'),
            new ImportCommand\Product\Create('2'),
        ];

        $this->client->getProductApi()->addUpsertResponse('identifier', 1, 422, '', 1);

        $this->importer->import($commands);

        $expected = [
            [
                'api' => 'product',
                ['identifier' => '1'],
                ['identifier' => '2'],
            ],
            [
                'api' => 'product',
                ['identifier' => '1'],
            ],
        ];

        $requestLog = $this->client->getRequestLog();

        $this->assertSame($requestLog, $expected);
    }
}
