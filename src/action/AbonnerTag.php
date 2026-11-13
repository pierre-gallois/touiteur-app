<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\RepoDev;
use \InvalidArgumentException;

/**
 * Action qui abonne l'utilisateur connecté à un tag saisi sur son mur (fonctionnalité 11)
 */
class AbonnerTag extends Action {

    public function execute(): string {

        $repo = RepoDev::getInstance();

        // Récupération des identifiants
        $pseudo = $_SESSION['utilisateur'];
        // On retire les espaces et le # possible, vu que les tags sont enregistrés sans le # (ex : "testTag" et pas "#testTag")
        $libelleTag = ltrim(trim($_POST['libelleTag'] ?? ''), '#');

        // Récupération des objets
        $utilisateur = $repo->getUtilisateur($pseudo);
        try {

            $tag = $repo->getTag($libelleTag);
        } catch (InvalidArgumentException) {
            return "<p>Ce tag n'existe pas</p>";
        }

        // Abonnement
        $utilisateur->suivreTag($tag);

        return "<p>Abonnement au tag effectué !</p>";
    }

}