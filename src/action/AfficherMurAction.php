<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\render\RendererTouite;
use iutnc\touiteur\repository\RepoDev;

/**
 * Action qui affiche le mur de l'utilisateur connecté avec le formulaire pour s'abonner à un tag
 */
class AfficherMurAction extends Action {

    public function execute(): string {

        $repo = RepoDev::getInstance();

        // Récupération des identifiants
        $pseudo = $_SESSION['utilisateur']->pseudo; // on a l'objet Utilisateur dans la session, donc on prend son pseudo

        // Récupération de l'utilisateur
        $utilisateur = $repo->getUtilisateur($pseudo);

        // structure html
        $html = "<h1>Mon mur</h1>";

        // le formulaire pour s'abonner à un tag (sur son mur l'utilisateur peut entrer un tag)
        $html .= <<<HTML
<form method="POST" action="index.php?action=abonner-tag">
    <label for="libelleTag">S'abonner à un tag :</label>
    <input type="text" name="libelleTag" id="libelleTag" placeholder="#bug" required>
    <button type="submit">S'abonner</button>
</form>
HTML;

        // On affiche la liste des tags suivis par l'utilisateur
        $listeTags = "";
        foreach ($utilisateur->tagsSuivis as $tag) {
            $libelle = filter_var($tag->libelleTag, FILTER_SANITIZE_SPECIAL_CHARS); // pour éviter les injections XSS
            $listeTags .= "#" . $libelle . " ";
        }
        if ($listeTags === "") {
            $html .= "<p>Vous ne suivez aucun tag pour l'instant.</p>";
        } else {
            $html .= "<p>Tags suivis : " . $listeTags . "</p>";
        }

        // On affiche les touites du mur (= touites des utilisateurs suivis et des tags suivis)
        $html .= RendererTouite::afficherMurUtilisateur($utilisateur, $repo->getTouites());

        return $html;
    }

}
