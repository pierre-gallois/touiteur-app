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
$u1 = new Utilisateur(new Profil("Jean", "Pierre", "jean.pierre@gmail.com"), "JeanPi");
echo "<h1>Test afficher court</h1>";
$touite = new Touite("Je découvris un paysage entièrement recouvert de neige. Les rues semblaient silencieuses, les arbres scintillaient sous les premiers rayons du soleil et, pour un instant, le temps paraissait s'être arrêté.", $u, "10.09.2016");
echo RendererTouite::rendererCourt($touite, $touite->id);

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
    new Touite("Il ne faut jamais abandonner simplement parce que les choses deviennent difficiles.", $u1, "11.11.2018"),
    new Touite("Les rêves sont comme des étoiles : même lorsqu'on ne les voit pas, ils continuent de briller quelque part dans le ciel.", $u, "14.02.2019"),
    new Touite("Nouvelle journée, nouvelles possibilités !", $u, "08.11.2020"),
    new Touite("Le meilleur moyen de progresser en programmation est de pratiquer régulièrement.", $u1, "13.11.2020"),
    new Touite("Le meilleur moment pour commencer un projet était hier. Le deuxième meilleur moment, c'est maintenant. Alors pourquoi attendre encore ?", $u, "21.05.2022"),
    new Touite("Le travail d'équipe permet de réaliser des projets beaucoup plus ambitieux.", $u1, "09.11.2023"),
    new Touite("La vie est une aventure pleine de surprises, de rencontres inattendues et de petits moments qui deviennent de grands souvenirs.", $u, "03.11.2025"),
    new Touite("Chaque jour est une nouvelle occasion d'apprendre quelque chose et de devenir meilleur.", $u1, "04.11.2025"),
    new Touite("Le bonheur se trouve souvent dans les choses les plus simples de la vie.", $u1, "05.11.2025"),
    new Touite("La technologie évolue tellement vite qu'il faut constamment apprendre de nouvelles choses.", $u1, "06.11.2025"),
    new Touite("Un bon développeur n'est pas celui qui ne fait jamais d'erreurs, mais celui qui sait les résoudre.", $u1, "07.11.2025"),
    new Touite("Parfois, prendre du recul permet de trouver la solution à un problème qui semblait impossible.", $u1, "08.11.2025"),
    new Touite("La cybersécurité est un domaine passionnant où chaque jour apporte de nouveaux défis.", $u1, "10.11.2025"),
    new Touite("Un petit progrès chaque jour peut mener à de grandes réussites.", $u1, "12.11.2025")];

// A voir sur le navigateur si ça affiche bien
echo RendererTouite::afficherListeTouite($liste);

foreach ($liste as $touite) {
    $auteur = $touite->auteur;
    $auteur->ajouterTouite($touite);
}

echo "<h1>Test afficher touite d'un utilisateur</h1>";
echo RendererTouite::aff_touite_utilisateur($u1);

