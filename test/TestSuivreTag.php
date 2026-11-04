<?php

require_once __DIR__ . '/../vendor/autoload.php';

use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\classes\Profil;
use iutnc\touiteur\exception\TestFailedException;
use iutnc\touiteur\classes\Tag;

function test_Suivre() {
   // Préparation des données
   // Tag qui vas être suivi
   $tag = new Tag("123456","#test","Ceci est un test");

   // Celui qui va suivre
   $profil = new Profil("Galkine", "Antonin", "e99013u@etu.univ-lorraine.fr");
   $moi    = new Utilisateur($profil, "Gvlky");


   // Méthode à tester
   $moi->suivreTag($tag);

   // Vérifications
   $suivis = $moi->tagsSuivis;
   if (in_array($tag, $suivis, true)) {
        echo "Test réussi : le tag est bien suivi.";
    } else {
        echo "Test échoué : le tag n'a pas été ajouté.";
    }

}

test_Suivre();