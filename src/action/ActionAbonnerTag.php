<?php

namespace iutnc\touiteur\classes\actions;

use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\classes\Tag;

class ActionAbonnerTag {

    public function execute(Utilisateur $utilisateur, Tag $tag) : void {

        // L'utilisateur suit le tag
        $utilisateur->suivreTag($tag);

        // Récupération du repository
        $repo = RepoDev::getInstance();

        // Enregistrement de l'utilisateur et du tag
        $repo->stocker($utilisateur);
        $repo->stocker($tag);
    }
}