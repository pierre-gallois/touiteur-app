<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\RepoDev;
use \InvalidArgumentException;

/**
 * Action qui permet à l'utilisateur connecté de suivre l'auteur d'un touite
 */
class ActionSuivreUtilisateur extends Action {

    public function execute(): string {

        $repo = RepoDev::getInstance();

        // récupération des identifiants
        $monPseudo = $_SESSION['utilisateur']->pseudo; // on a l'objet Utilisateur dans la session, donc on prend son pseudo
        $pseudoASuivre = filter_input(INPUT_POST, 'pseudo'); // renvoie false si ce n'est pas un simple texte, exmple :  (marche pas : Pierre Magnier) (marche : PierreMagnier)

        // lien vers son mur (affichent les touites des utilisateurs suivis)
        $lienMur = "<a href='index.php?action=mur'>Voir mon mur</a>";

        // récupération de l'utilisateur connecté
        try {
            $utilisateur = $repo->getUtilisateur($monPseudo);
        } catch (InvalidArgumentException) {
            // nouvel utilisateur (= il n'est pas dans le JSON) on prend celui de la session (comme pour le mur)
            $utilisateur = $_SESSION['utilisateur'];
        }

        // récupération de l'utilisateur à suivre
        if (!$pseudoASuivre) {
            return "<p>Cet utilisateur n'existe pas</p>";
        }
        try {
            $utilisateurASuivre = $repo->getUtilisateur($pseudoASuivre);
        } catch (InvalidArgumentException) {
            return "<p>Cet utilisateur n'existe pas</p>";
        }

        // on ne peut pas se suivre soi meme
        if ($utilisateurASuivre->pseudo === $monPseudo) {
            return "<p>Vous ne pouvez pas vous suivre vous-même</p>" . $lienMur;
        }

        // abonnement
        $utilisateur->suivreUtilisateur($utilisateurASuivre);

        $pseudoAffiche = filter_var($utilisateurASuivre->pseudo, FILTER_SANITIZE_SPECIAL_CHARS);
        return "<p>Vous suivez maintenant " . $pseudoAffiche . " !</p>" . $lienMur;
    }

}
