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
            return '';
        }

        // Récupération du repository
        $repo = RepoDev::getInstance();

        // Recherche de l'utilisateur
        try {
            $utilisateur = $repo->getUtilisateur($pseudo);

        } catch (\InvalidArgumentException $e) {

            // Si l'utilisateur n'existe pas
            if ($nom === '' || $prenom === '' || $email === '') {
                return '';
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

        // Redirection vers l'accueil
        header('Location: index.php');
        exit();
    }
}
