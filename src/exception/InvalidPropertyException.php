<?php

namespace iutnc\touiteur\exception;

use \Exception;
use \Throwable;

/**
 * Classe InvalidPropertyException
 */
class InvalidPropertyException extends Exception
{
    /**
     * Constructeur qui construit une InvalidPropertyException
     */
    public function __construct(string $message = "", int $code = 0, Throwable|null $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
