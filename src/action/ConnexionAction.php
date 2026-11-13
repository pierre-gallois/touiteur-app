<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\classes\Profil;
use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\repository\RepoDev;

/**
 * Gère l'affichage du formulaire et la connexion d'un utilisateur.
 */
class ConnexionAction extends Action
{
    public function execute(): string
    {
        // Une simple visite de la page login (GET) affiche le formulaire.
        if ($this->http_method !== 'POST') {
            return self::formulaireConnexion();
        }

        $pseudo = trim($_POST['pseudo'] ?? '');
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($pseudo === '') {
            return self::formulaireConnexion('Veuillez saisir un pseudo.');
        }

        $repo = RepoDev::getInstance();

        // Si le pseudo existe déjà, on reconnecte cet utilisateur.
        try {
            $utilisateur = $repo->getUtilisateur($pseudo);
        } catch (\InvalidArgumentException $e) {
            // Sinon, les informations de profil sont nécessaires pour créer le compte.
            if ($nom === '' || $prenom === '' || $email === '') {
                return self::formulaireConnexion(
                    'Ce pseudo est inconnu. Pour créer un compte, renseignez le nom, le prénom et l’email.'
                );
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return self::formulaireConnexion('Adresse email invalide.');
            }

            $profil = new Profil($nom, $prenom, $email);
            $utilisateur = new Utilisateur($profil, $pseudo);
            $repo->stocker($utilisateur);
        }

        // On démarre la session si nécessaire et on mémorise l'utilisateur.
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        session_regenerate_id(true);
        $utilisateur->connecter();
        $_SESSION['utilisateur'] = $utilisateur;
        // On vide les tags suivis de la session précédente (sinon un autre utilisateur les garderait)
        $_SESSION['tagsSuivis'] = [];

        // Redirection après connexion pour éviter de renvoyer le formulaire.
        header('Location: index.php');
        exit;
    }

    private static function formulaireConnexion(string $erreur = ''): string
    {
        $message = $erreur !== ''
            ? '<p class="erreur">' . htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') . '</p>'
            : '';

        return $message . <<<HTML
<h1>Bienvenue sur Touiteur</h1>
<h2>Connexion</h2>

<form method="POST" action="index.php?action=login">
    <label for="pseudo">Pseudo :</label>
    <input type="text" name="pseudo" id="pseudo" required>

    <label for="nom">Nom (uniquement pour créer un compte) :</label>
    <input type="text" name="nom" id="nom">

    <label for="prenom">Prénom (uniquement pour créer un compte) :</label>
    <input type="text" name="prenom" id="prenom">

    <label for="email">Email (uniquement pour créer un compte) :</label>
    <input type="email" name="email" id="email">

    <button type="submit">Se connecter</button>
</form>
HTML;
    }
}
