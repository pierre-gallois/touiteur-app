<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\RepositoryBD;

class AfficherListeUtilisateursAction extends Action
{
    // Affiche la liste des utilisateurs
    public function execute(): string
    {
        // Récupération du repository
        $repo = RepositoryBD::getInstance();

        // Récupération de tous les utilisateurs
        $utilisateurs = $repo->getUtilisateurs();

        // Début de la page HTML
        $html = '<h1>Liste des utilisateurs</h1>';
        $html .= '<div>';

        // Parcours des utilisateurs
        foreach ($utilisateurs as $utilisateur) {

            // Récupération du pseudo et protection des caractères spéciaux
            $pseudo = filter_var($utilisateur->pseudo, FILTER_SANITIZE_SPECIAL_CHARS);

            // Création du lien vers les touites de cet utilisateur
            $html .= '<p>';
            $html .= '<a href="index.php?action=touites-utilisateur&pseudo=' . urlencode($utilisateur->pseudo) . '">';
            $html .= $pseudo;
            $html .= '</a>';
            $html .= '</p>';
        }

        $html .= '</div>';

        return $html;
    }
}
