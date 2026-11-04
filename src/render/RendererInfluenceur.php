<?php

namespace iutnc\touiteur\render;

use iutnc\touiteur\classes\Utilisateur;

/**
 * Classe RendererInfluenceur permettant d'afficher les influenceurs
 */
class RendererInfluenceur {
    /**
     * Méthode rendererInfluenceur qui affiche la liste des influenceurs
     * @param array $utilisateurs liste des utilisateurs
     * @return string la liste des influenceurs au format html
     */
    public static function rendererInfluenceur(array $utilisateurs) : string
    {
        // On utilise usor pour que la méthode trie les utilisateurs selon leur nombre d'abonnés
        // Ou en cas d'égalité par leur pseudo
        usort($utilisateurs, function (Utilisateur $u, Utilisateur $u1): int {
            // On calcule le nombre d'abonnés de chaque utilisateur
            $nbAbonnesU = count($u->utilisateursAbonnes);
            $nbAbonnesU1 = count($u1->utilisateursAbonnes);

            // si nbAbonnesU est plus grand que nbAbonnesU1
            if ($nbAbonnesU > $nbAbonnesU1) {
                return 1; // comme ça u sera après u1
            } else if ($nbAbonnesU < $nbAbonnesU1) { // sinon si c'est u1 qui a le plus d'abonnés
                return -1; // alors c'est u1 qui va après u
            } // comme ça l'ordre croissant est maintenu
            // si le nombre d'abonnés est identique on compare les noms d'utilisateur
            else {
                return strcmp($u->pseudo, $u1->pseudo);
            }
        });

        // Structure html
        $html = "<Div>";
        $html .= "<p>Liste des influenceurs : </p>";
        $html .= "<ul>";

        // On affiche les utilisateurs triés de la liste
        foreach ($utilisateurs as $u) {
            $nbAbonnes = count($u->utilisateursAbonnes);
            $pseudo = filter_var($u->pseudo, FILTER_SANITIZE_SPECIAL_CHARS); // pour éviter les injections XSS
            $html .= "<li> $pseudo, nombre d'abonnés : $nbAbonnes </li>";
        }

        $html .= "</ul></Div>";
        return $html;
    }
}