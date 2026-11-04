<?php

namespace iutnc\touiteur\classes\actions;

use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\classes\Touite;
use iutnc\touiteur\exception\LengthTouiteException;

class PublierTouite {

    public function execute(Utilisateur $utilisateur, string $message): void {

        // Création du touite
        $touite = new Touite(
            $message,
            $utilisateur,
            date('Y-m-d H:i:s')
        );

        // Ajout du touite à l'utilisateur
        $utilisateur->ajouterTouite($touite);
    }
}