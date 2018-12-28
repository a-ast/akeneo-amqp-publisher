<?php

namespace Aa\AkeneoImport\CommandBus;

use Aa\AkeneoImport\CommandBus\Transport\Receiver;
use Aa\AkeneoImport\ImportCommands\CommandListHandlerInterface;
use Aa\AkeneoImport\ImportCommands\Product\UpdateProduct;
use Aa\AkeneoImport\ImportCommands\ProductModel\UpdateProductModel;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Component\Messenger\Worker;

class Consumer
{
    /**
     * @var Receiver
     */
    private $receiver;

    public function __construct(Receiver $receiver)
    {
        $this->receiver = $receiver;
    }

    public function consume(CommandListHandlerInterface $handler)
    {
        $receive = $this->receiver->receive(UpdateProduct::class);

        foreach ($receive as $commandList) {

            try {

                $handler->handle($commandList);

            } catch (\Exception $e) {

                $receive->throw($e);


            }

        }


    }
}
