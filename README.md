# Akeneo Import (DEPRECATED)

**DEPRECATED** Please consider using [Akeneo Data Loader](https://github.com/a-ast/akeneo-data-loader)

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
* Batch data transformation (e.g. if you can't implement mass actions in Akeneo e.g. in Serenity edition).


## Installation
```
composer require aa/akeneo-import
```

## Examples

### Create a simple product via API
 
```php
$command = new Product\Create('tshirt-red-xl');

$importer = (new ApiImporterFactory())->createByCredentials('http://akeneo', 'client_id', 'secret', 'user', 'pass');
$importer->import([$command]);

``` 

### Mass import using message broker 

For mass imports you can use message broker like RabbitMQ.

#### Publish commands:

```php

$command = new Product\Create('tshirt-red-xl');

$queue = (new QueueFactory())->createByDsn('dsn://mq', 'messages');

$queue->enqueue($command);

``` 

#### Consume commands and redirect them to Akeneo API:

```php
$queue = (new QueueFactory())->createByDsn('dsn://mq', 'messages');

$importer = (new ApiImporterFactory())->createByCredentials('http://akeneo', 'client_id', 'secret', 'user', 'pass');
$importer->importQueue([$command]);

```  


## See Also

* [Command Query Separation](https://martinfowler.com/bliki/CommandQuerySeparation.html)
