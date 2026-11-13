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
            var_dump($_FILES);
            $message = trim($_POST['message'] ?? '');
            $imagePath = null;
            $erreurImage = null;

            // Vérifier si une image a été envoyée
            if (isset($_FILES['image']) &&
                $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {

                // Vérifier les erreurs d'envoi
                if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                    $erreurImage = "Erreur lors de l'envoi de l'image.";
                }

                // Vérifier la taille (maximum 5 Mo)
                elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                    $erreurImage = "L'image ne doit pas dépasser 5 Mo.";
                }

                else {
                    // Vérifier le véritable type du fichier
                    $finfo = new \finfo(FILEINFO_MIME_TYPE);
                    $mime = $finfo->file($_FILES['image']['tmp_name']);

                    $extensions = [
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/webp' => 'webp',
                        'image/gif' => 'gif'
                    ];

                    if (!isset($extensions[$mime])) {
                        $erreurImage = "Format d'image non accepté.";
                    } else {
                        // Générer un nom unique pour l'image
                        $nomImage = bin2hex(random_bytes(16))
                            . '.' . $extensions[$mime];

                        // Chemin du dossier où stocker l'image
                        $dossier = dirname(__DIR__, 2)
                            . '/public/uploads/';

                        // Créer le dossier s'il n'existe pas
                        if (!is_dir($dossier)) {
                            mkdir($dossier, 0755, true);
                        }

                        // Déplacer l'image dans le dossier uploads
                        if (move_uploaded_file(
                            $_FILES['image']['tmp_name'],
                            $dossier . $nomImage
                        )) {
                            $imagePath = 'uploads/' . $nomImage;
                        } else {
                            $erreurImage = "Impossible d'enregistrer l'image.";
                        }
                    }
                }
            }

            // Vérifier que le message n'est pas vide
            if ($message === "") {
                $html .= "<p>Un message ne peut pas être vide.</p>";
            }

            // Afficher une éventuelle erreur d'image
            elseif ($erreurImage !== null) {
                $html .= "<p>$erreurImage</p>";
            }

            else {
                // Création du touite
                try {
                    $touite = new Touite(
                        $message,
                        $utilisateur,
                        date('Y-m-d H:i:s'),
                        $imagePath
                    );

                    // Ajout du touite à l'utilisateur
                    $utilisateur->ajouterTouite($touite);

                    // Enregistrement du touite dans RepoDev
                    $repo->stocker($touite);

                    // Rendu HTML
                    $html .= "<p>Touite publié !</p>";

                } catch (LengthTouiteException $e) {
                    $html .= "<p>{$e->getMessage()}</p>";
                }
            }
        }

        // Zone pour écrire et publier son touite
        $html .= <<<HTML
        <form method="POST" action="" enctype="multipart/form-data">

            <textarea name="message"
                      rows="5"
                      maxlength="235"
                      placeholder="Message ..."></textarea>

            <br><br>

            <label for="image">Ajouter une image :</label>
            <input type="file"
                   name="image"
                   id="image"
                   accept="image/jpeg,image/png,image/webp,image/gif">

            <br><br>

            <input type="submit" value="Publier">

        </form>
        HTML;

        // On affiche le touite si l'utilisateur vient de le poster
        if ($touite !== null) {
            $html .= "<p>Touite qui vient d'être posté :</p>";
            $html .= RendererTouite::aff_touite_detail($touite);
        }

        $html .= "<h1>Touites déjà postés :</h1>";
        $html .= RendererTouite::aff_touite_utilisateur($utilisateur);

        return $html;
    }
}