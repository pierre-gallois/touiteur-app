<?php

namespace iutnc\touiteur\classes\actions;

use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\classes\Tag;

class AbonnerTag {

    public function execute(Utilisateur $utilisateur, Tag $tag): void {

        // L'utilisateur suit le tag
        $utilisateur->suivreTag($tag);
    }
}