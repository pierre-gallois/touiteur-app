<?php

namespace iutnc\touiteur\exception;

use Exception;

class DispatcherException extends Exception {
    public function __construct(string $message = "") {
        parent::__construct($message);
    }
}
