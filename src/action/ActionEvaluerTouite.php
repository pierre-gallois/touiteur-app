<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\render\RendererTouite;
use iutnc\touiteur\repository\RepoDev;
use \InvalidArgumentException;

/**
 * Action qui permet à l'utilisateur connecté de noter un touite qu'il consulte
 */
class ActionEvaluerTouite extends Action {

    public function execute(): string {

        $repo = RepoDev::getInstance();

        // Récupération des identifiants
        $pseudo = $_SESSION['utilisateur']->pseudo; // on a l'objet Utilisateur, donc on prend son pseudo
        $idTouite = $_POST['idTouite'] ?? '';
        $choix = $_POST['choix'] ?? '';

        // Récupération du touite
        try {
            $touite = $repo->getTouite($idTouite);
        } catch (InvalidArgumentException) {
            return "<p>Ce touite n'existe pas</p>";
        }

        // fonctionnement des note : approuvé = +1 et rejeté = -1 (on refuse le reste, au cas ou quelqu'un modifie le formulaire)
        if ($choix === 'approuve') {
            $noteAjoutee = $touite->noter($pseudo, 1);
        } elseif ($choix === 'rejete') {
            $noteAjoutee = $touite->noter($pseudo, -1);
        } else {
            return "<p>Ce choix de note n'existe pas</p>";
        }

        // On affiche si la note a été prise en compte ou non (un utilisateur ne peut noter qu'une fois le meme touite)
        if ($noteAjoutee) {
            $html = "<p>Note prise en compte !</p>";
        } else {
            $html = "<p>Vous avez déjà noté ce touite</p>";
        }

        // on réaffiche le touite en détail avec sa nouvelle note
        $html .= RendererTouite::aff_touite_detail($touite);
        return $html;
    }

}
