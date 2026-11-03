<?php
/**
 * Classe RendererTouite qui permet d'afficher un touite sous différente forme
 */
class RendererTouite{

    /**
     *méthode  pour afficher le touite en detail
     *param  touite récupere le touite pour l'afficher
     * récupere de touite le pseudo , le message et la date en utilisant un filtre
     */
    public function  aff_touite_detail(Touite $touite){
        $pseudo = filter_var(
            $touite->auteur->pseudo,
            FILTER_SANITIZE_SPECIAL_CHARS
        );
        $message = filter_var(
            $touite->message,
            FILTER_SANITIZE_SPECIAL_CHARS
        );

        $date = filter_var(
            $touite->date,
            FILTER_SANITIZE_SPECIAL_CHARS
        );
        $html = "<article class='touite-detail'>";
        $html .= "<h2>" . $pseudo . "</h2>";
        $html .= "<p>" . $message . "</p>";
        $html .= "<div>" . $date . "</div>";
        $html .= "</article>";

        return $html;

    }

    
}
