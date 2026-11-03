<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use iutnc\touiteur\repository\RepoDev;
use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\classes\Profil;
use iutnc\touiteur\classes\Touite;

// Instancier un Repo
$repo = RepoDev::getInstance();

// Préparer les données
$profil      = new Profil("Mousque", "Eloune", "eloune.mousque@touiteur.fr");
$utilisateur = new Utilisateur($profil, "E.Mousque");
$touite      = new Touite("Je vais racheter Touiteur !", $utilisateur, "01/01/2022");

// Enregistrer un Touite
$id = $repo->enregistrer($touite);

// Récupérer les données du Touite :


