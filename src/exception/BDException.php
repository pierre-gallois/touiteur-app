<?php

namespace iutnc\touiteur\exception;

use \Exception;
use \Throwable;

/**
 * Classe BDException pour une erreur venant de la base de données (ex : refus d'un trigger)
 */
class BDException extends Exception {
    /**
     * Constructeur qui construit une BDException
     * @param string $message
     * @param int $code
     * @param null null
     */
    public function __construct(string $message = "", int $code = 0, Throwable|null $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
