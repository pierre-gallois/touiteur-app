<?php

require_once __DIR__ . '/../vendor/autoload.php';

use iutnc\touiteur\action\AbonnerTag;
use iutnc\touiteur\action\PublierTouite;
use iutnc\touiteur\action\AfficherInfluenceurAction;
use iutnc\touiteur\action\AfficherListeTouiteAction;
use iutnc\touiteur\action\AfficherListeUtilisateursAction;
use iutnc\touiteur\action\AfficherTouiteDetailAction;
use iutnc\touiteur\action\AfficherTouitesUtilisateurAction;
use iutnc\touiteur\action\ConnexionAction;
use iutnc\touiteur\action\DefaultAction;
use iutnc\touiteur\dispatcher\Dispatcher;
use iutnc\touiteur\exception\DispatcherException;
use iutnc\touiteur\repository\RepoDev;

// La session doit être démarrée avant toute lecture/écriture de $_SESSION.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Chargement des données de démonstration.
$repo = RepoDev::getInstance();
$repo->chargerDonnees(__DIR__ . '/../config/dev_dummy_data.json');

// Après chaque requête, RepoDev est reconstruit.
// On réinjecte donc l'utilisateur connecté conservé dans la session.
if (isset($_SESSION['utilisateur_obj'])) {
    $repo->stocker($_SESSION['utilisateur_obj']);
}

$dispatcher = (new Dispatcher())
    ->addRoute('login', ConnexionAction::class)
    ->addRoute('abonner-tag', AbonnerTag::class)
    ->addRoute('publier-touite', PublierTouite::class)
    ->addRoute('detail', AfficherTouiteDetailAction::class)
    ->addRoute('touites-utilisateur', AfficherTouitesUtilisateurAction::class)
    ->addRoute('liste-utilisateurs', AfficherListeUtilisateursAction::class)
    ->addRoute('touites', AfficherListeTouiteAction::class)
    ->addRoute('show-influenceurs', AfficherInfluenceurAction::class)
    ->addRoute('default', DefaultAction::class);

$action = $_GET['action'] ?? 'default';
$estConnecte = isset($_SESSION['utilisateur_obj']) && $_SESSION['utilisateur_obj'] instanceof \iutnc\touiteur\classes\Utilisateur;

// Un visiteur non connecté ne peut accéder qu'au formulaire de connexion.
if (!$estConnecte && $action !== 'login') {
    echo (new ConnexionAction())->execute();
    exit;
}

try {
    echo $dispatcher->run();
} catch (DispatcherException $e) {
    http_response_code(404);
    echo '<p>Page introuvable.</p>';
}
