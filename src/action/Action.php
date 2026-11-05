<?php

namespace iutnc\touiteur\action;

/**
 * Classe Action
 */
abstract class Action {

    protected ?string $http_method = null;
    protected ?string $hostname = null;
    protected ?string $script_name = null;

    /**
     * Constructeur Action
     */
    public function __construct() {
        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
    }

    /**
     * Methode qui exécute l'action demandée
     * @return string le code html pour afficher le résultat de l'action
     */
    abstract public function execute() : string;
}