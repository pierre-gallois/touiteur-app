<?php

require_once __DIR__ . '/../vendor/autoload.php';

use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\classes\Profil;

// Celui qui va etre suivi
$profil      = new Profil("Mousque", "Eloune", "eloune.mousque@touiteur.fr");
$eloune = new Utilisateur($profil, "E.Mousque");

// Celui qui va suivre
$profil = new Profil("Galkine", "Antonin", "e99013u@etu.univ-lorraine.fr");
$moi    = new Utilisateur($profil, "Gvlky");

$moi->suivreUtilisateur($eloune);

echo "Pseudo de la personne suivie : " . $moi->utilisateursSuivis[0]->pseudo;


