<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\classes\Profil;
use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\repository\RepoDev;

class ConnexionAction extends Action
{
    public function execute(): string
    {
        // Récupération des informations du formulaire
        $pseudo = trim($_POST['pseudo'] ?? '');
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');

        // Vérification du pseudo
        if ($pseudo === '') {
            return "<p>Veuillez saisir un pseudo.</p><a href='index.php'>retour à la connexion</a>";
        }

        // Récupération du repository
        $repo = RepoDev::getInstance();

        // Recherche de l'utilisateur
        try {
            $utilisateur = $repo->getUtilisateur($pseudo);

        } catch (\InvalidArgumentException $e) {

            // Si l'utilisateur n'existe pas
            if ($nom === '' || $prenom === '' || $email === '') {
                return "<p>Ce pseudo existe pas</p>"
                    . "<a href='index.php'>retour à la connexion</a>";
            }
            // Vérification de l'adresse email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return "<p>Adresse email invalide.</p><a href='index.php'>retour à la connexion</a>";
            }

            // Création du profil
            $profil = new Profil($nom, $prenom, $email);

            // Création de l'utilisateur
            $utilisateur = new Utilisateur($profil, $pseudo);

            // Enregistrement de l'utilisateur
            $repo->stocker($utilisateur);
        }

        // Connexion de l'utilisateur
        $utilisateur->connecter();
        $_SESSION['utilisateur'] = $utilisateur;

        // Redirection vers l'accueil
        header('Location: index.php');
        exit();
    }
}
