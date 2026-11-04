<?php

require_once __DIR__ . '/../vendor/autoload.php';

use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\classes\Profil;
use iutnc\touiteur\exception\TestFailedException;

function test_Suivre() {
   // Préparation des données
   // Celui qui va etre suivi
   $profil      = new Profil("Mousque", "Eloune", "eloune.mousque@touiteur.fr");
   $eloune = new Utilisateur($profil, "E.Mousque");

   // Celui qui va suivre
   $profil = new Profil("Galkine", "Antonin", "e99013u@etu.univ-lorraine.fr");
   $moi    = new Utilisateur($profil, "Gvlky");


   // Méthode à tester
   $moi->suivreUtilisateur($eloune);

   // Vérification
   $suivis = $moi->utilisateursSuivis;
   if (count($suivis) !== 1) {
       throw new TestFailedException("L'utilisateur \"\$moi\" devrait avoir 1 personne suivie. Obtenu : " . count($suivis));
   }
   if ($suivis[0]->pseudo !== "E.Mousque") {
       throw new TestFailedException("L'utilisateur \"\$moi\" devrait suivre \"E.Mousque\". Obtenu : " . $suivis[0]->pseudo);
   }
}
