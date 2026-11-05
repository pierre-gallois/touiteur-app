<?php
require_once __DIR__ . '/../vendor/autoload.php';

use iutnc\touiteur\action\AfficherTouiteDetailAction;
use iutnc\touiteur\action\AfficherTouitesUtilisateurAction;
use iutnc\touiteur\action\ActionAbonnerTag;
use iutnc\touiteur\action\ActionPublierTouite;
use iutnc\touiteur\dispatcher\Dispatcher;
use iutnc\touiteur\exception\DispatcherException;
use iutnc\touiteur\action\AfficherListeTouiteAction;
use iutnc\touiteur\action\AfficherInfluenceurAction;
use iutnc\touiteur\action\DefaultAction;
use iutnc\touiteur\repository\RepoDev;
use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\classes\Profil;
use \iutnc\touiteur\classes\Touite;

// Bases de données (en dur)
// Sera remplacée dans un config plus tard
$repo = RepoDev::getInstance();
try {
    // Création de quelques utilisateurs
    $jean = new Utilisateur(new Profil("Jean", "Dupont", "jdupont@dupont.jean"), "JeanDu08");
    $repo->stocker($jean);
    $repo->stocker(new Touite("Je suis Jean", $jean, "04.11.2026 17.09.44"));

} catch (\iutnc\touiteur\exception\LengthTouiteException $e) {
    echo $e->getMessage();
}

// Création Dispatcher
$dispatcher = (new Dispatcher())
    ->addRoute('abonner-tag', ActionAbonnerTag::class)
    ->addRoute('publier-touite', ActionPublierTouite::class)
    ->addRoute('touite-detail', AfficherTouiteDetailAction::class)
    ->addRoute('touites-utilisateur', AfficherTouitesUtilisateurAction::class)
    ->addRoute('touites', AfficherListeTouiteAction::class)
    ->addRoute('show-influenceurs', AfficherInfluenceurAction::class)
    ->addRoute('default', DefaultAction::class);

// Connecté
if (session_status() === PHP_SESSION_ACTIVE) {
    // Exécution action
    try {
        echo $dispatcher->run();
    } catch (DispatcherException $e) {
        echo "404 : Not found.";
    }
}


