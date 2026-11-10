<?php

namespace iutnc\touiteur\action;
use iutnc\touiteur\repository\RepoDev;
use iutnc\touiteur\render\RendererTouite;

class AfficherTouitesUtilisateurAction extends Action {

    // Exécute l'action permettant d'afficher les touites d'un utilisateur
    public function execute() : string
    {
        // Récupération du repository
        $repo = RepoDev::getInstance();

        // Récupère le pseudo de l'utilisateur depuis l'URL
        $pseudo = $_GET['pseudo'] ?? '';

        // Recherche de l'utilisateur
        try {
            $utilisateur = $repo->getUtilisateur($pseudo);

            // Affichage des touites de l'utilisateur
            return RendererTouite::aff_touite_utilisateur($utilisateur);

        } catch (\InvalidArgumentException $e) {
            return '<p>Utilisateur introuvable.</p>';
        }
    }
}