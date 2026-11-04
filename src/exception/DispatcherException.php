<?php

namespace iutnc\touiteur\exception;

use Exception;
use Throwable;

class DispatchException extends Exception {
    public function __construct(string $message = "") {
        parent::__construct($message);
    }
}
