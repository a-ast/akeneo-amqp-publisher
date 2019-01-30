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

    const RESPONSE_MESSAGES_OF_RECOVERABLE_COMMANDS = [
        '/^Property "parent" expects a valid parent code\.$/',
        '/^Product model "(.*)" does not exist\.$/',
        '/^Product "(.*)" does not exist\.$/',
    ];

    public function handle(CommandInterface $command, int $responseCode, string $message, CommandCallbacks $callbacks = null, array $errors = [])
    {
        if ($this->isSuccess($responseCode)) {
            return;
        }

        if ($this->isRecoverable($responseCode, $message) && $callbacks !== null) {
            $callbacks->repeat($command);

            return;
        }

        throw new CommandHandlerException($message, $command, $responseCode, $errors);
    }

    private function isRecoverable(int $responseCode, string $message): bool
    {
        return (self::UNPROCESSABLE_ENTITY === $responseCode &&
            $this->isMessageOfRecoverableCommand($message));
    }

    private function isMessageOfRecoverableCommand(string $message): bool
    {
        foreach (self::RESPONSE_MESSAGES_OF_RECOVERABLE_COMMANDS as $messageExpression) {
            if (1 === preg_match($messageExpression, $message)) {
                return true;
            }
        }

        return false;
    }

    private function isSuccess(int $responseCode)
    {
        return in_array($responseCode, [self::CREATED, self::NO_CONTENT]);
    }
}
