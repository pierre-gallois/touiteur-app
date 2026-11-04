<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\render\RendererTouite;

class AfficherTouitesUtilisateurAction extends Action {
    // Tableau contenant les utilisateurs de l'application
    private array $listeUtilisateurs;

    // Constructeur : récupère la liste des utilisateurs
    public function __construct(array $listeUtilisateurs) {
        $this->listeUtilisateurs = $listeUtilisateurs;
    }

    // Exécute l'action permettant d'afficher les touites d'un utilisateur
    public function execute() : string
    {
        // Récupère le pseudo de l'utilisateur depuis l'URL
        $pseudo = $_GET['pseudo'] ?? '';

        // Parcourt la liste des utilisateurs
        foreach ($this->listeUtilisateurs as $utilisateur) {

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