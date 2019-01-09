# Akeneo Import

Akeneo Import is a library that simplifies data import to Akeneo PIM.

Using this library you can create easy-to-use commands to modify product data and related PIM entities
like categories.
It supports synchronous import via Akeneo API or asynchronous import 
using a message broker like RabbitMQ.


## How you can use it

* Import from external systems (legacy PIM or regular data provider). 
* Mass media file import. 
* Import from older Akeneo versions.
* Data generation for testing, local development or performance benchmarking.
* Batch data manipulations (e.g. if you can't implement mass actions in Akeneo e.g. in Serenity edition).


## Installation
```
composer require a-ast/akeneo-import

```

## Examples

### Create a simple product directly via API
 
```php
$command = new UpdateOrCreateProduct('tshirt-red-xl');

$importerFactory = new ImporterFactory();
$importer => $importerFactory->create();

$handlerFactory = new ApiCommandHandlerFactory();
$handler = $handlerFactory->createByCredentials('http://akeneo', 'client_id', 'secret', 'user', 'pass');

$importer->import(new ArrayObject([$command]), $handler);

``` 

### Create a simple product and publish it to a message queue

```php

$command = new UpdateOrCreateProduct('tshirt-red-xl');

$importerFactory = new ImporterFactory();
$importer => $importerFactory->create();

$handlerFactory = new AmqpCommandHandlerFactory();
$handler = $handlerFactory->createByDsn('dsn://mq');

$importer->import(new ArrayObject([$command]), $handler);

``` 

It will publish a command to a message queue.
To read messages and create products using Akeneo API you would need a consumer:

```php

$consumerFactory = new ConsumerFactory();
$consumer => $consumerFactory->createByDsn('dsn://mq');

$handlerFactory = new ApiCommandHandlerFactory();
$handler = $handlerFactory->createByCredentials('http://akeneo', 'client_id', 'secret', 'user', 'pass');

$importer->import(new ArrayObject([$command]), $handler);

```  


## See Also

* [Command Query Separation](https://martinfowler.com/bliki/CommandQuerySeparation.html)
