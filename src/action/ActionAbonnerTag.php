<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\RepoDev;

class ActionAbonnerTag {

    public function execute(): string {

        $repo = RepoDev::getInstance();

        // Récupération des identifiants
        $pseudo = $_SESSION['pseudo'];
        $idTag = $_POST['idTag'] ?? '';

        // Récupération des objets
        $utilisateur = $repo->getUtilisateur($pseudo);
        $tag = $repo->getTag($idTag);

        // Abonnement
        $utilisateur->suivreTag($tag);

        return "Abonnement au tag effectué !";
    }

}