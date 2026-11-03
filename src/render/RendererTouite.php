<?php

namespace iutnc\touiteur\render;

use iutnc\touiteur\classes\Touite;

/**
 * Classe RendererTouite qui permet d'afficher un touite sous différente forme
 */
class RendererTouite
{
    /**
     * Méthode rendererCourt
     * @return touite en format court en code html
     */
    public function rendererCourt(Touite $touite): string
    {
        // comme c'est un affiche court, on affiche que les 100 premiers caractères du touite
        $texteCourt = mb_strimwidth($touite->message, 0, 100, "...");

        // Pour la sécurité on passe tout par filter_var
        $pseudo = filter_var($touite->auteur->pseudo, FILTER_SANITIZE_SPECIAL_CHARS);
        $date = filter_var($touite->date, FILTER_SANITIZE_SPECIAL_CHARS);
        $texteCourt = filter_var($texteCourt, FILTER_SANITIZE_SPECIAL_CHARS);

        $html = "<p>$pseudo</p>";
        $html .= "<p>$texteCourt</p>";
        $html .= "<p>$date</p>";

        return $html;
    }
}
