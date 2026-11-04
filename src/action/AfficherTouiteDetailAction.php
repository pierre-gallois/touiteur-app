<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\render\RendererTouite;

class AfficherTouiteDetailAction implements Action
{
    // Tableau contenant tous les touites disponibles
    private array $listeTouites;

    // Constructeur : récupère la liste des touites
    public function __construct(array $listeTouites)
    {
        $this->listeTouites = $listeTouites;
    }

    // Exécute l'action permettant d'afficher un touite en détail
    public function execute(): string
    {
        // Récupère l'identifiant du touite dans l'URL
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        // Vérifie que l'identifiant existe
        if (
            $id === null ||
            $id === false ||
            !array_key_exists($id, $this->listeTouites)
        ) {
            // Retourne un message si le touite est introuvable
            return '<p>Touite introuvable.</p>';
        }

        // Récupère le touite correspondant à l'identifiant et lance la fonction de l'affichage
        return RendererTouite::aff_touite_detail(
            $this->listeTouites[$id]
        );
    }
}