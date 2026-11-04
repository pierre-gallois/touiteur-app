<?php

require_once __DIR__ . '/../vendor/autoload.php';

use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\classes\Profil;
use iutnc\touiteur\exception\TestFailedException;


/**
 * Fonction qui vérifie la methode suivreUtilisateur.
 * Elle vérifie que la personne est bien ajoutée à l'attribut $utilisateursSuivis
 */
function test_normal() {
    // Préparation des données
    // (celui qui va etre suivi)
    $profil = new Profil("Mousque", "Eloune", "eloune.mousque@touiteur.fr");
    $eloune = new Utilisateur($profil, "E.Mousque");

    // (celui qui va suivre)
    $profil = new Profil("Galkine", "Antonin", "e99013u@etu.univ-lorraine.fr");
    $moi    = new Utilisateur($profil, "Gvlky");


    // Méthode à tester
    $moi->suivreUtilisateur($eloune);

    // Vérifications
    $suivis = $moi->utilisateursSuivis;
    // test : ajoute bel et bien
    if (count($suivis) !== 1) {
        throw new TestFailedException("L'utilisateur \"\$moi\" devrait avoir 1 personne suivie. Obtenu : " . count($suivis));
    }
    // test : ajoute bel et bien un utilisateur
    if ($suivis[0]->pseudo !== "E.Mousque") {
        throw new TestFailedException("L'utilisateur \"\$moi\" devrait suivre \"E.Mousque\". Obtenu : " . $suivis[0]->pseudo);
    }
}

function test_doublon() {
    // Préparation des données
    // Celui qui va etre suivi
    $profil = new Profil("Mousque", "Eloune", "eloune.mousque@touiteur.fr");
    $eloune = new Utilisateur($profil, "E.Mousque");

    // Celui qui va suivre
    $profil = new Profil("Galkine", "Antonin", "e99013u@etu.univ-lorraine.fr");
    $moi    = new Utilisateur($profil, "Gvlky");


    // Méthode à tester
    $moi->suivreUtilisateur($eloune);
    $moi->suivreUtilisateur($eloune); // (doublon à tester)

    // Vérifications
    $suivis = $moi->utilisateursSuivis;
    // test : ne l'a ajouté qu'une fois
    if (count($suivis) !== 1) {
        throw new TestFailedException("Les doublons devrait être ignorés.");
    }
}

try {
    test_normal();
    test_doublon();
    echo "Tests passés avec succès.";
} catch (TestFailedException $e) {
    echo "Test raté : " . $e->getMessage();
}
