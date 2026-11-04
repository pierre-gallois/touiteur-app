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

$dispatcher = (new Dispatcher())
    ->addRoute('Abonner Tag', ActionAbonnerTag::class)
    ->addRoute('Publier Touite', ActionPublierTouite::class)
    ->addRoute('Afficher tout le touite', AfficherTouiteDetailAction::class)
    ->addRoute('Afficher touite(s) utilisateur', AfficherTouitesUtilisateurAction::class)
    ->addRoute('afficher liste touite', AfficherListeTouiteAction::class)
    ->addRoute('Afficher Influenceur', AfficherInfluenceurAction::class);

try {
    echo $dispatcher->run();
} catch (DispatcherException $e) {
    echo "Erreur d'affichage";
}


