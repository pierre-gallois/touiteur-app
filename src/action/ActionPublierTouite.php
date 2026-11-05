<?php

namespace iutnc\touiteur\classes\actions;

use iutnc\touiteur\repository\RepoDev;
use iutnc\touiteur\classes\Touite;
use iutnc\touiteur\exception\LengthTouiteException;

class ActionPublierTouite {

    public function execute(): string {

        $repo = RepoDev::getInstance();

        // Récupération du message
        $message = trim($_POST['message'] ?? '');

        // Récupération de l'utilisateur connecté
        $utilisateur = $repo->getUtilisateur($_SESSION['pseudo']);

        if ($message === '') {
            return "Le message ne peut pas être vide.";
        }

        try {
            $touite = new Touite(
                $message,
                $utilisateur,
                date('Y-m-d H:i:s')
            );

            $utilisateur->ajouterTouite($touite);
            $repo->stocker($touite);

            return "Touite publié avec succès !";

        } catch (LengthTouiteException $e) {
            return $e->getMessage();
        }
    }
}