<?php

namespace iutnc\touiteur\exception;

use Exception;

/**
 * Classe NoFoundException
 */
class NoFoundException extends Exception
{
    public function __construct(string $message = "")
    {
        parent::__construct($message);
    }
}