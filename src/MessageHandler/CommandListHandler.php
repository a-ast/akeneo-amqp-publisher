<?php

namespace Aa\AkeneoImport\MessageHandler;

use Aa\AkeneoImport\Exception\RejectMessageException;
use Aa\AkeneoImport\ImportCommands\CommandListHandlerInterface;
use Aa\AkeneoImport\ImportCommands\CommandListInterface;
use Aa\AkeneoImport\ImportCommands\Exceptions\CommandHandlerException;
use Aa\AkeneoImport\ImportCommands\Exceptions\RecoverableCommandHandlerException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\Transport\AmqpExt\Exception\RejectMessageExceptionInterface;
use Throwable;

class CommandListHandler implements MessageHandlerInterface
{
    /**
     * @var CommandListHandlerInterface
     */
    private $handler;

    public function __construct(CommandListHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @throws \Aa\AkeneoImport\Exception\RejectMessageException
     * @throws \Throwable
     */
    public function __invoke(CommandListInterface $commands)
    {
        try {
            $this->handler->handle($commands);

        } catch (RecoverableCommandHandlerException $e) {

            throw $e;

        } catch (CommandHandlerException $e) {

            throw new RejectMessageException($e->getMessage(), $e->getCode(), $e);

        } catch (Throwable $e) {

            throw $e;
        }

    }
}
