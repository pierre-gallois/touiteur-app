<?php
/**
 * Fichir TestException pour tester les exceptions qu'on a crée
 */
require_once __DIR__ . '/../vendor/autoload.php';

use \iutnc\touiteur\exception\LengthTouiteException;
use \iutnc\touiteur\exception\InvalidPropertyException;
use \iutnc\touiteur\classes\Utilisateur;
use \iutnc\touiteur\classes\Profil;
use \iutnc\touiteur\classes\Touite;

// Vérification que si un touite fait plus de 235 caractères l'exception est levée
echo "<h1>Test LengthTouiteException </h1>";
try {
    $u = new Utilisateur(new Profil("Dupont", "Gérard", "dupont.gerard@gmail.com"), "SuperGérard");
    // Message de 400 caractères dépassant la limite de 235
    $message = "Au cœur d'une ville ancienne, un jeune inventeur découvrit un mystérieux carnet oublié dans le grenier de son grand-père. Chaque page contenait des plans incroyables et des formules étranges. Fasciné par ces découvertes, il décida de construire une machine capable de voyager dans le temps. Après plusieurs nuits de travail acharné, son invention prit enfin vie. Mais lorsqu'il activa le mécanisme, une lumière éblouissante envahit la pièce et tout changea.";
    $touite = new Touite($message, $u, "10.09.2016");
} catch (LengthTouiteException $e){
    echo "Exception levée";
}

// Vérification que si on essaye d'appeler un attribut
echo "<h1>Test InvalidPropertyException</h1>";
try{
    $u = new Utilisateur(new Profil("Dupont", "Gérard", "dupont.gerard@gmail.com"), "SuperGérard");
    $u->super; // attribut qui n'existe pas dans Utilisateur
} catch(InvalidPropertyException $e){
    echo "Exception levée";
}