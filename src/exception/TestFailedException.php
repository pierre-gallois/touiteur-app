<?php


namespace iutnc\touiteur\exception;

use \Exception;
use \Throwable;

/**
 * Classe lengthTouiteException pour la taille d'un touite
 */
class TestFailedException extends Exception
{
    /**
     * Constructeur qui construit une LengthTouiteException
     */
    public function __construct(string $message = "", int $code = 0, Throwable|null $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
