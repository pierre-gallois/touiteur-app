<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\RepoDev;
use \InvalidArgumentException;

/**
 * Action qui abonne l'utilisateur connecté à un tag saisi sur son mur (fonctionnalité 11)
 */
class ActionAbonnerTag extends Action {

    public function execute(): string {

        $repo = RepoDev::getInstance();

        // Récupération des identifiants
        // On retire les espaces et le # possible, vu que les tags sont enregistrés sans le # (ex : "testTag" et pas "#testTag")
        $libelleTag = ltrim(trim($_POST['libelleTag'] ?? ''), '#');

        // Lien pour revenir sur le mur (c'est là où se trouve le formulaire)
        $lienMur = "<a href='index.php?action=mur'>Retour à mon mur</a>";

        // Récupération des objets
        try {
            $tag = $repo->getTag($libelleTag);
        } catch (InvalidArgumentException) {
            return "<p>Ce tag n'existe pas</p>" . $lienMur;
        }

        // On crée la liste des tags suivis dans la session si elle n'existe pas encore
        if (!isset($_SESSION['tagsSuivis'])) {
            $_SESSION['tagsSuivis'] = [];
        }

        // On vérifie que l'utilisateur n'est pas déjà abonné à ce tag
        if (in_array($tag->libelleTag, $_SESSION['tagsSuivis'])) {
            return "<p>Vous etes déjà abonné à ce tag là</p>" . $lienMur;
        }

        //on garde dans la session, sinon il on le perd à la page suivante
        $_SESSION['tagsSuivis'][] = $tag->libelleTag;

        return "<p>Abonnement au tag effectué !</p>" . $lienMur;
    }

}