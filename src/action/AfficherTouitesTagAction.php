<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\render\RendererTouite;
use iutnc\touiteur\repository\RepoDev;

/**
 * Action qui affiche les touites associés à un tag accessible quand on clique sur un tag
 */
class AfficherTouitesTagAction extends Action {

    public function execute(): string {

        $repo = RepoDev::getInstance();

        // Récupération du tag dans l'url
        $libelleTag = filter_input(INPUT_GET, 'tag'); // renvoie false si ce n'est pas un simple texte
        if (!$libelleTag) {
            return "<p>Tag introuvable.</p>";
        }

        // Pour la sécurité on passe le tag par filter_var avant de l'afficher
        $libelleAffiche = filter_var($libelleTag, FILTER_SANITIZE_SPECIAL_CHARS);

        // Structure html
        $html = "<h1>Touites avec #" . $libelleAffiche . "</h1>";

        // On affiche les touites du tag avec la méthode du renderer
        $html .= RendererTouite::afficherTouitesParTag($repo->getTouites(), $libelleTag);

        return $html;
    }

}
