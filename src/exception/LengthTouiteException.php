<?php

namespace iutnc\touiteur\exception;

use \Exception;
use \Throwable;

/**
 * Classe lengthTouiteException pour la taille d'un touite
 */
class LengthTouiteException extends Exception {
    /**
     * Constructeur qui construit une LengthTouiteException
     * @param string $message
     * @param int $code
     * @param null null
     */
    public function __construct(string $message = "", int $code = 0, Throwable|null $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
