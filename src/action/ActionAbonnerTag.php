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
        $pseudo = $_SESSION['utilisateur']->pseudo; // on a l'objet Utilisateur dans la session, donc on prend son pseudo
        $libelleTag = filter_input(INPUT_POST, 'libelleTag'); // renvoie false si ce n'est pas un simple texte (ex : un tableau)
        if (!$libelleTag) {
            return "<p>Ce tag n'existe pas</p><a href='index.php?action=mur'>Retour à mon mur</a>";
        }
        // On retire les espaces et le # possible, vu que les tags sont enregistrés sans le # (ex : "testTag" et pas "#testTag")
        $libelleTag = ltrim(trim($libelleTag), '#');

        // Récupération des objets
        try {
            $utilisateur = $repo->getUtilisateur($pseudo);
        } catch (InvalidArgumentException) {
            // si c'est un nouvel utilisateur (= il n'est pas dans le JSON) on prend celui de la session (comme pour le mur)
            $utilisateur = $_SESSION['utilisateur'];
        }
        try {
            $tag = $repo->getTag($libelleTag);
        } catch (InvalidArgumentException) {
            return "<p>Ce tag n'existe pas</p><a href='index.php?action=mur'>Retour à mon mur</a>";
        }

        // Abonnement
        $utilisateur->suivreTag($tag);

        return "<p>Abonnement au tag effectué !</p><a href='index.php?action=mur'>Retour à mon mur</a>";
    }

}