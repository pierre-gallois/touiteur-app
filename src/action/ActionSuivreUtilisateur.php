<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\RepositoryBD;
use iutnc\touiteur\exception\BDException;
use \InvalidArgumentException;

/**
 * Action qui permet à l'utilisateur connecté de suivre l'auteur d'un touite
 */
class ActionSuivreUtilisateur extends Action {

    public function execute(): string {

        $repo = RepositoryBD::getInstance();

        // récupération des identifiants
        $monPseudo = $_SESSION['utilisateur']->pseudo; // on a l'objet Utilisateur dans la session, donc on prend son pseudo
        $pseudoASuivre = filter_input(INPUT_POST, 'pseudo'); // renvoie false si ce n'est pas un simple texte, il refuse seulement un tableau (pseudo[]=x)

        // lien vers son mur (affichent les touites des utilisateurs suivis)
        $lienMur = "<a href='index.php?action=mur'>Voir mon mur</a>";

        // récupération de l'utilisateur connecté
        $utilisateur = $repo->getUtilisateur($monPseudo);

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

        // on vérifie qu'il ne le suit pas déjà
        $dejaSuivi = false;
        foreach ($utilisateur->utilisateursSuivis as $utilisateurSuivi) {
            if ($utilisateurSuivi->pseudo === $utilisateurASuivre->pseudo) {
                $dejaSuivi = true;
            }
        }
        if ($dejaSuivi) {
            return "<p>Vous suivez déjà cet utilisateur</p>" . $lienMur;
        }

        // abonnement (enregistré dans la base de données)
        try {
            $repo->suivreUtilisateur($utilisateur, $utilisateurASuivre);
        } catch (BDException $e) {
            // refus de la base (trigger : se suivre soi-même ou même email)
            return "<p>" . filter_var($e->getMessage(), FILTER_SANITIZE_SPECIAL_CHARS) . "</p>" . $lienMur;
        }

        $pseudoAffiche = filter_var($utilisateurASuivre->pseudo, FILTER_SANITIZE_SPECIAL_CHARS);
        return "<p>Vous suivez maintenant " . $pseudoAffiche . " !</p>" . $lienMur;
    }

}
