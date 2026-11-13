<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\classes\Touite;
use iutnc\touiteur\exception\LengthTouiteException;
use iutnc\touiteur\render\RendererTouite;
use iutnc\touiteur\repository\RepoDev;

/**
 * Classe PublierTouiteAction qui permet l'intéraction avec l'utilisateur de publier un touite
 */
class ActionPublierTouite extends Action {

    /**
     * Méthode publierTouite qui publie un touite d'un utilisateur
     * @return string un code html affichant le touite en détail pour montrer qu'il a bien été posté
     * @throws LengthTouiteException si le touite dépasse 235 caractères
     */
   public function execute(): string
   {
       // Vérifier que l'utilisateur est connecté
       if (!isset($_SESSION['utilisateur_obj'])) {
           return "<p>Erreur : vous devez être connecté.</p>";
       }

       // Récupérer l'objet Utilisateur depuis la session
       $utilisateur = $_SESSION['utilisateur_obj'];

       // Récupérer le repository
       $repo = RepoDev::getInstance();

       // Réenregistrer l'utilisateur dans le repository
       $repo->stocker($utilisateur);

       // Structure HTML
       $html = "<h1>Publier un touite</h1>";

       $touite = null;
        // On regarde si le formulaire a bien été envoyé
        if ($this->http_method === 'POST') {
            $message = $_POST['message'];

            if ($message === "") {
                $html .= "<p>Un message ne peut pas être vide</p>";
            } else {
                // Création du touite
                try {
                    $touite = new Touite(
                        $message,
                        $utilisateur,
                        date('Y-m-d H:i:s')
                    );

                // Ajout du touite à l'utilisateur
                $utilisateur->ajouterTouite($touite);
                // Enregistrement du touite dans RepoDev
                    $repo->stocker($touite);


                    // rendu html
                $html .= "<p>Touite publié !</p>";
                } catch (LengthTouiteException $e) {
                    $html .= "<p>{$e->getMessage()}</p>";
                }

            }
        }

        // zone pour écrire et publié son touite
        $html .= <<<HTML
<form method="POST" action="">

        <textarea name="message"
                  rows="5"
                  cols="30"
                  maxlength="235"
                  placeholder="Message ..."></textarea>

    <br>

    <input type="submit" value="Publier">

</form>
HTML;
        // On affiche le touite si l'utilisateur vient de le poster
        if ($touite !== null) {
            $html .= "<p>Touite qui vient d'être posté : </p>";
            $html .= RendererTouite::aff_touite_detail($touite);
        }

        $html .= "<h1>Touites déjà postés :</h1>";
        $html .= RendererTouite::aff_touite_utilisateur($utilisateur);
        return $html;
    }
}