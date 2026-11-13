<?php
require_once __DIR__ . '/../vendor/autoload.php';

use iutnc\touiteur\action\AfficherListeUtilisateursAction;
use iutnc\touiteur\action\AfficherTouiteDetailAction;
use iutnc\touiteur\action\AfficherTouitesUtilisateurAction;
use iutnc\touiteur\action\ActionAbonnerTag;
use iutnc\touiteur\action\ActionPublierTouite;
use iutnc\touiteur\action\ConnexionAction;
use iutnc\touiteur\dispatcher\Dispatcher;
use iutnc\touiteur\exception\DispatcherException;
use iutnc\touiteur\action\AfficherListeTouiteAction;
use iutnc\touiteur\action\AfficherInfluenceurAction;
use iutnc\touiteur\action\DefaultAction;
use iutnc\touiteur\repository\RepoDev;

// Démarrage de la session
session_start();


// Chargement des données
$repo = RepoDev::getInstance();
$repo->chargerDonnees(__DIR__ . '/../config/dev_dummy_data.json');


// Création Dispatcher
$dispatcher = (new Dispatcher())
    ->addRoute('login', ConnexionAction::class)
    ->addRoute('abonner-tag', ActionAbonnerTag::class)
    ->addRoute('publier-touite', ActionPublierTouite::class)
    ->addRoute('detail', AfficherTouiteDetailAction::class)
    ->addRoute('touites-utilisateur', AfficherTouitesUtilisateurAction::class)
    ->addRoute('liste-utilisateurs', AfficherListeUtilisateursAction::class)
    ->addRoute('touites', AfficherListeTouiteAction::class)
    ->addRoute('show-influenceurs', AfficherInfluenceurAction::class)
    ->addRoute('default', DefaultAction::class);
// Action demandée
$action = $_GET['action'] ?? 'default';
// Le contenu est calculé AVANT tout affichage (sinon header() de ConnexionAction ne marcherait plus)
$connecte = !empty($_SESSION['utilisateur']);

// Vérifie si l'utilisateur est connecté
if ($connecte || $action === "login") {

    // Exécution de l'action
    try {
        $contenu = $dispatcher->run();
    } catch (DispatcherException $e) {
        $contenu = "404 : Not found.";
    }

} else {

    // Affichage de la page de connexion
    $contenu = '
    <h1>Bienvenue sur Touiteur</h1>

    <h2>Connexion</h2>

    <form method="POST" action="index.php?action=login">

        <label for="pseudo">Pseudo :</label>
        <input type="text" name="pseudo" id="pseudo" required>

        <label for="nom">Nom :</label>
        <input type="text" name="nom" id="nom">

        <label for="prenom">Prénom :</label>
        <input type="text" name="prenom" id="prenom">

        <label for="email">Email :</label>
        <input type="email" name="email" id="email">

        <button type="submit">Se connecter</button>

    </form>
    ';
}

// permet d'avoir un bon affichage css : squelette HTML minimal qui charge la feuille de style
$classeBody = $connecte ? '' : 'page-connexion';

echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Touiteur</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="$classeBody">
    <main>
        $contenu
    </main>
</body>
</html>
HTML;
