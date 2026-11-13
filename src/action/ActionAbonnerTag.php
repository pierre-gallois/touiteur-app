<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\RepositoryBD;
use \InvalidArgumentException;

/**
 * Action qui abonne l'utilisateur connecté à un tag saisi sur son mur (fonctionnalité 11)
 */
class ActionAbonnerTag extends Action {

    public function execute(): string {

        $repo = RepositoryBD::getInstance();

        // Récupération des identifiants
        $pseudo = $_SESSION['utilisateur']->pseudo; // on a l'objet Utilisateur dans la session, donc on prend son pseudo
        $libelleTag = filter_input(INPUT_POST, 'libelleTag'); // renvoie false si ce n'est pas un simple texte (ex : un tableau)
        if (!$libelleTag) {
            return "<p>Ce tag n'existe pas</p><a href='index.php?action=mur'>Retour à mon mur</a>";
        }
        // On retire les espaces et le # possible, vu que les tags sont enregistrés sans le # (ex : "testTag" et pas "#testTag")
        $libelleTag = ltrim(trim($libelleTag), '#');

        // Récupération des objets
        $utilisateur = $repo->getUtilisateur($pseudo);
        try {
            $tag = $repo->getTag($libelleTag);
        } catch (InvalidArgumentException) {
            return "<p>Ce tag n'existe pas</p><a href='index.php?action=mur'>Retour à mon mur</a>";
        }

        // On vérifie que l'utilisateur n'est pas déjà abonné à ce tag (sinon le tag serait en double)
        $dejaAbonne = false;
        foreach ($utilisateur->tagsSuivis as $tagSuivi) {
            if ($tagSuivi->libelleTag === $tag->libelleTag) {
                $dejaAbonne = true;
            }
        }
        if ($dejaAbonne) {
            return "<p>Vous êtes déjà abonné à ce tag</p><a href='index.php?action=mur'>Retour à mon mur</a>";
        }

        // Abonnement (enregistré dans la base de données)
        $repo->suivreTag($utilisateur, $tag);

        return "<p>Abonnement au tag effectué !</p><a href='index.php?action=mur'>Retour à mon mur</a>";
    }

}