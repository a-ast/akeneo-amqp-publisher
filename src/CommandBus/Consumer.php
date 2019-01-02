<?php

namespace Aa\AkeneoImport\CommandBus;

use Aa\AkeneoImport\CommandBus\Transport\Receiver;
use Aa\AkeneoImport\ImportCommand\CommandListHandlerInterface;
use Aa\AkeneoImport\ImportCommand\Product\UpdateOrCreateProduct;


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
        $receive = $this->receiver->receive(UpdateOrCreateProduct::class);

        foreach ($receive as $commandList) {

            try {

                $handler->handle($commandList);

            } catch (\Exception $e) {

                $receive->throw($e);
            }
        }
    }
}
