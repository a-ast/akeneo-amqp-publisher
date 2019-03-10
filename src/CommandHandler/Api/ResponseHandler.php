<?php

namespace Aa\AkeneoImport\CommandHandler\Api;

use Aa\AkeneoImport\ImportCommand\CommandCallbacks;
use Aa\AkeneoImport\ImportCommand\CommandInterface;
use Aa\AkeneoImport\ImportCommand\Exception\CommandHandlerException;

class ResponseHandler
{
    const UNPROCESSABLE_ENTITY = 422;
    const CREATED = 201;
    const NO_CONTENT = 204;

    public function handleCommand(CommandInterface $command, int $responseCode, string $message, CommandCallbacks $callbacks = null, array $errors = [])
    {
        if ($this->isSuccess($responseCode)) {
            return;
        }

        if ($this->isRecoverable($responseCode) && $callbacks !== null) {
            $callbacks->repeat($command, $message, $responseCode);

            return;
        }

        throw new CommandHandlerException($message, $command, $responseCode, $errors);
    }

    public function handleCommands(array $commands, int $responseCode, string $message, CommandCallbacks $callbacks = null, array $errors = [])
    {
        if ($this->isSuccess($responseCode)) {
            return;
        }

        if ($this->isRecoverable($responseCode) && $callbacks !== null) {

            foreach ($commands as $command) {
                $callbacks->repeat($command, $message, $responseCode);
            }

            return;
        }

        // @todo: is it reasonable to select first command. add checks.
        throw new CommandHandlerException($message, $commands[0], $responseCode, $errors);
    }

    private function isRecoverable(int $responseCode): bool
    {
        return (self::UNPROCESSABLE_ENTITY === $responseCode);
    }

    private function isSuccess(int $responseCode)
    {
        return in_array($responseCode, [self::CREATED, self::NO_CONTENT]);
    }
}
