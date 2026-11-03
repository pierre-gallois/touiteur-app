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
     * @param touite à afficher
     * @return touite en format court en code html
     */
    public static function rendererCourt(Touite $touite): string
    {
        // comme c'est un affiche court, on affiche que les 100 premiers caractères du touite
        $texteCourt = mb_strimwidth($touite->message, 0, 100, "...");

        // Pour la sécurité on passe tout par filter_var
        $pseudo = filter_var($touite->auteur->pseudo, FILTER_SANITIZE_SPECIAL_CHARS);
        $date = filter_var($touite->date, FILTER_SANITIZE_SPECIAL_CHARS);
        $texteCourt = filter_var($texteCourt, FILTER_SANITIZE_SPECIAL_CHARS);

        // On construit le html
        $html = "<article>";
        $html .= "<p>$pseudo</p>";
        $html .= "<p>$texteCourt</p>";
        $html .= "<p>$date</p>";
        $html .= "</article>";

        return $html;
    }


    /**
     * Methode afficherListeTouite
     * @param touites la liste des touites
     * @return la liste des touites sous le format html
     */
    public static function afficherListeTouite(array $touites): string
    {
        // Pour afficher les touites dans l'ordre chronologique inverse il suffit de parcourir la liste mais à l'envers
        $touitesInverses = array_reverse($touites);

        // Pour la structure html
        $html = "<div>";

        // On vient afficher chaque touite en affichage court
        foreach ($touitesInverses as $touite) {
            $html .= self::rendererCourt($touite);
        }

        $html .= "</div>";
        return $html;
    }


    /**
     *méthode  pour afficher le touite en detail
     *param  touite récupere le touite pour l'afficher
     * récupere de touite le pseudo , le message et la date en utilisant un filtre
     */
    public static function  aff_touite_detail(Touite $touite){
        //filtre le pseudo de l'auteur
        $pseudo = filter_var(
            $touite->auteur->pseudo,
            FILTER_SANITIZE_SPECIAL_CHARS
        );
        //filtre le message du touite
        $message = filter_var(
            $touite->message,
            FILTER_SANITIZE_SPECIAL_CHARS
        );
        // filtre la date du touite
        $date = filter_var(
            $touite->date,
            FILTER_SANITIZE_SPECIAL_CHARS
        );

        //affichage du touite
        $html = "<article>";
        $html .= "<h2>" . $pseudo . "</h2>";
        $html .= "<p>" . $message . "</p>";
        $html .= "<div>" . $date . "</div>";
        $html .= "</article>";

        return $html;

    }


}
