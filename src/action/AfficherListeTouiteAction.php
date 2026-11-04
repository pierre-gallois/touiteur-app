<?php
namespace iutnc\touiteur\action;

use iutnc\touiteur\render\RendererTouite;

/**
 * Clase AfficherListeTouiteAction
 */
class AfficherListeTouiteAction extends Action{

    public function execute(): string
    {
        $touites = ;
        return "<h1>Touites</h1>" . RendererTouite::afficherListeTouite($touites);
    }


}