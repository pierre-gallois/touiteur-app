<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\classes\Touite;
use iutnc\touiteur\exception\LengthTouiteException;
use iutnc\touiteur\exception\NoFoundException;
use iutnc\touiteur\render\RendererTouite;
use iutnc\touiteur\repository\RepoDev;

/**
 * Classe PublierTouiteAction qui permet l'intéraction avec l'utilisateur de publier un touite
 */
class ActionPublierTouite extends Action
{

    /**
     * Méthode publierTouite qui publie un touite d'un utilisateur
     * @return string un code html affichant le touite en détail pour montrer qu'il a bien été posté
     * @throws LengthTouiteException si le touite dépasse 235 caractères
     * @throws NoFoundException si l'utilisateur n'existe pas en session
     */
    public function execute(): string
    {
        // On récupère l'utilisateur de la session
        $utilisateur = null;
        if (isset($_SESSION['utilisateur'])) {
            $utilisateur = $_SESSION['utilisateur'];
        } else {
            throw new NoFoundException("L'utilisateur n'a pas été trouvé");
        }

        // Création du touite
        $touite = new Touite(
            $message,
            $utilisateur,
            date('Y-m-d H:i:s')
        );

        // Ajout du touite à l'utilisateur
        $utilisateur->ajouterTouite($touite);

        // On enregistre le touite dans la base de donnée
        $repo = RepoDev::getInstance();
        $repo->stocker($touite);

        // Touite sous la forme html
        $html = RendererTouite::aff_touite_detail($touite);

        return <<<HTML
<div>
$html
</div>
HTML;

    }
}