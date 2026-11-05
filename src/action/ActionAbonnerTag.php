<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\classes\Tag;

class ActionAbonnerTag {

    public function execute(Utilisateur $utilisateur, Tag $tag) : void {

        // L'utilisateur suit le tag
        $utilisateur->suivreTag($tag);
    }
}