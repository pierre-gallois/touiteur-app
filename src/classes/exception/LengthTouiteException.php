<?php
namespace iutnc\touite\exception;
use \Exception;
use \Throwable;

/**
 * Classe lengthTouiteException pour la taille d'un touite
 */
class LengthTouiteException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable|null $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
