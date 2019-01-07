# Akeneo Import

Library for data import to Akeneo PIM.

@todo: preword about commands - https://martinfowler.com/bliki/CommandQuerySeparation.html

## Usage example

### Create a simple product directly via API
 
```php

$command = new UpdateOrCreateProduct('tshirt-red-xl');

$importerFactory = new ImporterFactory();
$importer => $importerFactory->build();
// @todo: wow, factory to hide dependencies?


$apiHandlerFactory = new ApiBatchHandlerFactory();
$apiHandler = $apiHandlerFactory->buildByCredentials('http://akeneo', 'client_id', 'secret', 'user', 'pass');
// @todo: remove required dependency

$importer->import(new ArrayObject([$command]), $apiHandler);

``` 

### Create a simple product and publish it to a message queue

```php

$command = new UpdateOrCreateProduct('tshirt-red-xl');

$importerFactory = new ImporterFactory();
$importer => $importerFactory->create();

$amqpHandlerFactory = new AmqpHandlerFactory();
$amqpHandler = $amqpHandlerFactory->createByDsn('dsn://mq');

$importer->import(new ArrayObject([$command]), $amqpHandler);

``` 

It will publish a command to a message queue.
To read messages and create products using Akeneo API you would need a consumer:

```php

$consumerFactory = new ConsumerFactory();
$consumer => $consumerFactory->create();
// $consumer = new Consumer();


// @todo: wow, factory to hide dependencies?


$apiHandlerFactory = new ApiBatchHandlerFactory();
$apiHandler = $apiHandlerFactory->buildByCredentials('http://akeneo', 'client_id', 'secret', 'user', 'pass');

$importer->import(new ArrayObject([$command]), $apiHandler);

```  
