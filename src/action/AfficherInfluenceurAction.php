<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\render\RendererInfluenceur;
use iutnc\touiteur\repository\RepositoryBD;

/**
 * Constructeur de AfficherInfluenceurAction
 */
class AfficherInfluenceurAction extends Action
{
    /**
     * Constructeur de AfficherInfluenceurAction
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Méthode execute qui va afficher la liste des influenceurs
     * @return string liste des influenceurs au format html
     */
    public function execute(): string
    {
        $repo = RepositoryBD::getInstance();
        $utilisateurs = $repo->getUtilisateurs();
        return "<h1>Influenceurs :</h1>" . RendererInfluenceur::rendererInfluenceur($utilisateurs);
    }
}