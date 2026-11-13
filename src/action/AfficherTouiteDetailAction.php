<?php

namespace iutnc\touiteur\action;
use iutnc\touiteur\repository\RepoBD;
use iutnc\touiteur\render\RendererTouite;

class AfficherTouiteDetailAction extends Action {

    // Exécute l'action permettant d'afficher un touite en détail
    public function execute() : string
    {
        // Récupère le repository
        $repo = RepoBD::getInstance();

        // Récupère l'identifiant du touite dans l'URL
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        // Vérifie que l'id est valide
        if ($id === null || $id === false) {
            return '<p>Touite introuvable.</p>';
        }
        // Récupère la liste des touites
        $listeTouites = $repo->getTouites();

        // Parcourt la liste des touites
        foreach ($listeTouites as $touite) {

            // Vérifie si l'id correspond à celui recherché
            if ($touite->id === $id) {

                // Demande au Renderer d'afficher le touite en détail
                return RendererTouite::aff_touite_detail($touite);
            }
        }
        // Si aucun touite n'a été trouvé
        return '<p>Touite introuvable.</p>';
    }
}