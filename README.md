# Akeneo Import

[![Build Status](https://travis-ci.org/a-ast/akeneo-import.svg?branch=master)](https://travis-ci.org/a-ast/akeneo-import)

Akeneo Import simplifies data import to Akeneo PIM.

Using this library you can create easy-to-use commands to modify product data and related PIM entities
like categories.
It supports synchronous import via Akeneo API or asynchronous import 
using message brokers like RabbitMQ.


## How you can use it

* Import from external systems (legacy PIM or regular data providers). 
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

$importer = (new ImporterFactory())->create();
$handler = (new ApiCommandHandlerFactory())
                ->createByCredentials('http://akeneo', 'client_id', 'secret', 'user', 'pass');

$importer->import([$command], $handler);

``` 

### Create a simple product and publish it to a message queue

```php

$command = new UpdateOrCreateProduct('tshirt-red-xl');

$importer = (new ImporterFactory())->create();
$handler = (new AmqpCommandHandlerFactory())->createByDsn('dsn://mq');

$importer->import([$command], $handler);

``` 

To read messages from the queue and create products using Akeneo API you need to create a consumer:

```php
$consumer = (new ConsumerFactory())->createByDsn('dsn://mq');

$handlerFactory = new ApiCommandHandlerFactory();
$handler = (new ApiCommandHandlerFactory())
                 ->createByCredentials('http://akeneo', 'client_id', 'secret', 'user', 'pass');

$consumer->consume($handler, $queueName);

```  


## See Also

* [Command Query Separation](https://martinfowler.com/bliki/CommandQuerySeparation.html)
