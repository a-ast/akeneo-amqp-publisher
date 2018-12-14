<?php

namespace Aa\AkeneoImport\Exception;

use Exception;
use Symfony\Component\Messenger\Transport\AmqpExt\Exception\RejectMessageExceptionInterface;

class RejectMessageException extends Exception implements RejectMessageExceptionInterface
{

}
