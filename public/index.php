
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

// Démarrage de la session
session_start();

// Chargement des données
$repo = RepoDev::getInstance();
$repo->chargerDonnees(__DIR__ . '/../config/dev_dummy_data.json');

// Gestion de la connexion
if (isset($_GET['action']) && $_GET['action'] === 'login') {

    $pseudo = $_POST['pseudo'] ?? '';

    try {
        $utilisateur = $repo->getUtilisateur($pseudo);
        $_SESSION['utilisateur'] = $utilisateur->pseudo;

        header('Location: index.php');
        exit();

    } catch (\InvalidArgumentException $e) {
        // Utilisateur introuvable
    }
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

// Vérifie si l'utilisateur est connecté
if (!empty($_SESSION['utilisateur'])) {

    // Exécution de l'action
    try {
        echo $dispatcher->run();
    } catch (DispatcherException $e) {
        echo "404 : Not found.";
    }

} else {

    // Page de connexion
    echo '
    <h1>Connexion à Touiteur</h1>

    <form method="POST" action="index.php?action=login">
        <label for="pseudo">Pseudo :</label>
        <input type="text" name="pseudo" id="pseudo" required>
        <button type="submit">Se connecter</button>
    </form>
    ';
}
