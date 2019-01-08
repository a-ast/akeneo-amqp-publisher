# Akeneo Import

Library for data import to Akeneo PIM.

@todo: preword about commands - https://martinfowler.com/bliki/CommandQuerySeparation.html

!Note: examples don't yet reflect the current state of the lib.

## Usage example

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
