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

        // Récupère la liste des utilisateurs
        $listeUtilisateurs = $repo->getUtilisateurs();

        // Parcourt la liste des utilisateurs
        foreach ($listeUtilisateurs as $utilisateur) {

            // Vérifie si le pseudo correspond à celui recherché
            if ($utilisateur->pseudo === $pseudo) {

                // Demande au Renderer d'afficher les touites
                return RendererTouite::aff_touite_utilisateur($utilisateur);
            }
        }

        // Retourne un message si aucun utilisateur n'a été trouvé
        return '<p>Utilisateur introuvable.</p>';
    }
}