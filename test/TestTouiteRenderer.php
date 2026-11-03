<?php

require_once __DIR__ . '/../vendor/autoload.php';

use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\classes\Profil;
use iutnc\touiteur\classes\Touite;
use iutnc\touiteur\render\RendererTouite;

/**
 * Fichier TestTouiteRenderer pour réaliser les différents tests de RendererTouite
 */

// On prépare les utilisateurs
$u = new Utilisateur(new Profil("Dupont", "Gérard", "dupont.gerard@gmail.com"), "SuperGérard");

echo "<h1>Test afficher court</h1>";
$touite = new Touite("Je découvris un paysage entièrement recouvert de neige. Les rues semblaient silencieuses, les arbres scintillaient sous les premiers rayons du soleil et, pour un instant, le temps paraissait s'être arrêté.", $u, "10.09.2016");
echo RendererTouite::rendererCourt($touite);

echo"<h1>Test afficher touite detail</h1>";
echo RendererTouite::aff_touite_detail($touite);

echo "<h1>Test Liste Touite</h1>";

// Création des touites
$liste = [
    new Touite("Le soleil se lève toujours après la nuit.", $u, "12.04.2012"),
    new Touite("Un café, un clavier et une journée de code.", $u, "23.08.2014"),
    new Touite("Parfois, il suffit d'un sourire pour changer une journée entière.", $u, "05.01.2016"),
    new Touite("La technologie avance vite, mais la curiosité humaine va encore plus loin. Chaque découverte ouvre une nouvelle porte vers l'inconnu.", $u, "17.06.2017"),
    new Touite("J'ai enfin compris mon bug après trois heures de recherche... il manquait un point-virgule.", $u, "29.09.2018"),
    new Touite("Les rêves sont comme des étoiles : même lorsqu'on ne les voit pas, ils continuent de briller quelque part dans le ciel.", $u, "14.02.2019"),
    new Touite("Nouvelle journée, nouvelles possibilités !", $u, "08.11.2020"),
    new Touite("Le meilleur moment pour commencer un projet était hier. Le deuxième meilleur moment, c'est maintenant. Alors pourquoi attendre encore ?", $u, "21.05.2022"),
    new Touite("La vie est une aventure pleine de surprises, de rencontres inattendues et de petits moments qui deviennent de grands souvenirs.", $u, "03.11.2025")
];

// A voir sur le navigateur si ça affiche bien
echo RendererTouite::afficherListeTouite($liste);

