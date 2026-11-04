<?php

namespace iutnc\touiteur\exception;

use \Exception;
use \Throwable;

/**
 * Classe InvalidTagException pour un libellé de tag invalide
 */
class InvalidTagException extends Exception {
    /**
     * Constructeur qui construit une InvalidTagException
     * @param string $message
     * @param int $code
     * @param null null
     */
    public function __construct(string $message = "", int $code = 0, Throwable|null $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}