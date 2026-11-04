<?php
require_once __DIR__ . '/../vendor/autoload.php';

use iutnc\touiteur\action\AfficherTouiteDetailAction;
use iutnc\touiteur\action\AfficherTouitesUtilisateurAction;
use iutnc\touiteur\classes\actions\ActionAbonnerTag;
use iutnc\touiteur\classes\actions\ActionPublierTouite;
use iutnc\touiteur\dispatcher\Dispatcher;
use iutnc\touiteur\exception\DispatcherException;
use iutnc\touiteur\action\AfficherListeTouiteAction;
use iutnc\touiteur\action\AfficherInfluenceurAction;
use iutnc\touiteur\action\DefaultAction;
use iutnc\touiteur\repository\RepoDev;

// Bases de données (en dur)
$repo = RepoDev::getInstance();
try {
    $repo->stocker(new \iutnc\touiteur\classes\Touite("Test", new \iutnc\touiteur\classes\Utilisateur(new \iutnc\touiteur\classes\Profil("Yo", "Le RAP", "YO.LERAP@gmail.com"), "TaSoeur"), "0.0.0.0"));
} catch (\iutnc\touiteur\exception\LengthTouiteException $e) {
    echo $e->getMessage();
}

// Création Dispatcher
$dispatcher = (new Dispatcher())
    ->addRoute('Abonner Tag', ActionAbonnerTag::class)
    ->addRoute('Publier Touite', ActionPublierTouite::class)
    ->addRoute('Afficher tout le touite', AfficherTouiteDetailAction::class)
    ->addRoute('Afficher touite(s) utilisateur', AfficherTouitesUtilisateurAction::class)
    ->addRoute('afficher liste touite', AfficherListeTouiteAction::class)
    ->addRoute('Afficher Influenceur', AfficherInfluenceurAction::class)
    ->addRoute('default', DefaultAction::class);

// Exécution action
try {
    echo $dispatcher->run();
} catch (DispatcherException $e) {
    echo "404 : Not found.";
}


