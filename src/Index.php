<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dispatcher=(new Dispatcher())*

->addRoute('Abonner Tag', ActionAbonnerTag::class)
->addRoute('Publier Touite', ActionPublierTouite::class)
->addRoute('Afficher tout le touite', AfficherTouiteDetailAction::class)
->addRoute('Afficher touite(s) utilisateur', AfficherTouitesUtilisateurAction::class)

try {
 echo $app->run();
} catch (RouterException $e) {
 echo "Erreur d'affichage";
}


