<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\render\RendererTouite;
use iutnc\touiteur\repository\RepositoryBD;

/**
 * Clase AfficherListeTouiteAction
 */
class AfficherListeTouiteAction extends Action {
    /**
     * Constructeur de AfficherListeTouiteAction
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Méthode execute qui va afficher la liste des touites
     * @return string liste des touites au format html
     */
    public function execute() : string
    {
        $repo = RepositoryBD::getInstance();
        $touites = $repo->getTouites();
        return "<h1>Touites</h1>" . RendererTouite::afficherListeTouite($touites);
    }
}