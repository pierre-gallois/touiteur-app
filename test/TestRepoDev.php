<?php

require_once __DIR__ . '../../vendor/autoload.php';

use iutnc\touiteur\repository\RepoDev;
use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\classes\Profil;
use iutnc\touiteur\classes\Touite;
use \iutnc\touiteur\exception\TestFailedException;

// Instancier un RepoDev
$repo = RepoDev::getInstance();

// Données préparées
$profil      = new Profil("Mousque", "Eloune", "eloune.mousque@touiteur.fr");
$utilisateur = new Utilisateur($profil, "E.Mousque");


/**
 * Vérifie la bonne initialisation du RepoDev
 */
function test0_initialisation() {
    global $repo;

    // Vérification
    if (!empty($repo->getUtilisateurs()))
        throw new TestFailedException("l'attribut \"\$utilisateurs\" devrait être vide");
    if (!empty($repo->getTouites()))
        throw new TestFailedException("l'attribut \"\$touites\" devrait être vide");
    if (!empty($repo->getTags()))
        throw new TestFailedException("l'attribut \"\$tags\" devrait être vide");
}

/**
 * Vérifie le bon fonctionnement de la methode enregistrerUtilisateur
 */
function test1_enregistrerUtilisateur() {
    global $repo;

    // Préparation des données
    global $utilisateur;

    // Méthode à tester (Enregistrer un Touite)
    $repo->stocker($utilisateur);

    // Vérifications
    $count = count($repo->getUtilisateurs());
    if ($count == 0)
        throw new TestFailedException("Aucun utilisateur n'a été ajouté dans l'attribut \"\$utilisateurs\"");
    elseif ($count != 1)
        throw new TestFailedException("L'utilisateur a été ajouté plusieurs fois : " . $count);
    // Vérification effets de bord
    if (!empty($repo->getTags()))
        throw new TestFailedException("Ajouter un utilisateur ne devrait pas ajouter de tags");
    if (!empty($repo->getTouites()))
        throw new TestFailedException("Ajouter un utilisateur ne devrait pas ajouter de touites");
}

/**
 * Vérifie qu'un touite n'est
 */
function test2_ajouterTouite_utilisateurConnu() {
    global $repo;

    // Préparation des données
    global $utilisateur;  // (utilisateur déjà dans le repo)
    $touite = new Touite("J'ai acheté Touiteur!", $utilisateur, "01.01.2001");

    // Methode à tester
    $repo->stocker($touite);

    // Vérification
    if (count($repo->getTouites()) == 0)
        throw new TestFailedException("Aucun touite n'a été ajouté.");
    if (count($repo->getTouites()) != 1 )
        throw new TestFailedException("Plusieurs touites on été ajoutés : " . count($repo->getTouites()));
    // Vérification effets de bord
    if (count($repo->getUtilisateurs()) != 1)
        throw new TestFailedException("Ajouter un touite ne devrait pas ajouter d'utilisateurs. (".count($repo->getUtilisateurs())." utilisateur(s), 1 attendu)");
    if (count($repo->getTags()) != 0)
        throw new TestFailedException("Ajouter un touite (sans tag) ne devrait pas ajouter de tag");
}

function test3_ajouterTouite_utilisateurInconnu() {
    global $repo;

    // Nouvel utilisateur (non enregistré);
    $profil = new Profil("Pesos", "George", "george.pesos@dollar.us");
    $utilisateur = new Utilisateur($profil, "PesosDu32");

    $touite = new Touite("Je suis riche", $utilisateur, "02.01.2001");

    // Methode à tester
    $repo->stocker($touite);

    // Vérification
    if (count($repo->getTouites()) != 1)
        throw new TestFailedException("Touiter doit necessiter d'être connu du repo.");
    if (count($repo->getTouites()) > 1 )
        throw new TestFailedException("Plusieurs touites on été ajoutés : " . (count($repo->getTouites()) - 1));
    if (count($repo->getUtilisateurs()) != 1)
        throw new TestFailedException("Touiter sans être connu (du repo) ne devrait pas ajouter d'utilisateurs");
}

function test4_ajouterTouite_avecTag() {
    global $repo;
    global $utilisateur;

    // Méthode à tester
    $repo->stocker(new Touite("J'ai une marque de véhicule éléctrique : #Edison", $utilisateur, "01.01.2001"));

    if (count($repo->getTags()) == 0)
        throw new TestFailedException("Aucun tag n'a été ajouté");
    if (count($repo->getTags()) != 1)
        throw new TestFailedException("Plusieurs tags on été ajoutés :" . count($repo->getTags()));
}

// Faire passer les tests
try {
    // Attention : l'ordre compte. Ne pas déplacer l'ordre des appels de fonctions.
    test0_initialisation();
    test1_enregistrerUtilisateur();
    test2_ajouterTouite_utilisateurConnu();
    test3_ajouterTouite_utilisateurInconnu();
    test4_ajouterTouite_avecTag();

    echo "Tests passés avec succès.";
} catch (TestFailedException $e) {
    echo "Test raté : " . $e->getMessage();
}