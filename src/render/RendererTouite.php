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


    }

    
}
