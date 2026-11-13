<?php

require_once __DIR__ . '/../vendor/autoload.php';

use iutnc\touiteur\action\ActionAbonnerTag;
use iutnc\touiteur\action\ActionPublierTouite;
use iutnc\touiteur\action\ActionEvaluerTouite;
use iutnc\touiteur\action\ActionSuivreUtilisateur;
use iutnc\touiteur\action\AfficherInfluenceurAction;
use iutnc\touiteur\action\AfficherListeTouiteAction;
use iutnc\touiteur\action\AfficherListeUtilisateursAction;
use iutnc\touiteur\action\AfficherMurAction;
use iutnc\touiteur\action\AfficherTouiteDetailAction;
use iutnc\touiteur\action\AfficherTouitesTagAction;
use iutnc\touiteur\action\AfficherTouitesUtilisateurAction;
use iutnc\touiteur\action\ConnexionAction;
use iutnc\touiteur\action\DefaultAction;
use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\dispatcher\Dispatcher;
use iutnc\touiteur\exception\DispatcherException;

// La session doit être démarrée avant toute lecture/écriture de $_SESSION.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// L'utilisateur est connecté si on a bien un objet Utilisateur dans la session (cf. ConnexionAction)
$connecte = isset($_SESSION['utilisateur']) && $_SESSION['utilisateur'] instanceof Utilisateur;

$dispatcher = (new Dispatcher())
    ->addRoute('login', ConnexionAction::class)
    ->addRoute('abonner-tag', ActionAbonnerTag::class)
    ->addRoute('publier-touite', ActionPublierTouite::class)
    ->addRoute('detail', AfficherTouiteDetailAction::class)
    ->addRoute('touites-utilisateur', AfficherTouitesUtilisateurAction::class)
    ->addRoute('liste-utilisateurs', AfficherListeUtilisateursAction::class)
    ->addRoute('touites', AfficherListeTouiteAction::class)
    ->addRoute('show-influenceurs', AfficherInfluenceurAction::class)
    ->addRoute('evaluer-touite', ActionEvaluerTouite::class)
    ->addRoute('mur', AfficherMurAction::class)
    ->addRoute('touites-tag', AfficherTouitesTagAction::class)
    ->addRoute('suivre-utilisateur', ActionSuivreUtilisateur::class)
    ->addRoute('default', DefaultAction::class);

$action = $_GET['action'] ?? 'default';

// Le contenu est calculé AVANT tout affichage (sinon header() de ConnexionAction ne marcherait plus)
if (!$connecte && $action !== 'login') {
    // Un visiteur non connecté ne peut accéder qu'au formulaire de connexion.
    $contenu = (new ConnexionAction())->execute();
} else {
    try {
        $contenu = $dispatcher->run();
    } catch (DispatcherException $e) {
        http_response_code(404);
        $contenu = '<p>Page introuvable.</p>';
    }
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
