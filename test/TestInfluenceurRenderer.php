<?php
require_once __DIR__ . '/../vendor/autoload.php';

use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\classes\Profil;
use iutnc\touiteur\render\RendererInfluenceur;

/**
 * TestInfluenceurRenderer pour tester l'affichage des influenceurs
 */

// On crée les utilisateurs
$u1 = new Utilisateur(new Profil("Martin", "Alice", "a.martin@gmail.com"), "Alice");
$u2 = new Utilisateur(new Profil("Martin", "LuterKing", "luterKing@gmail.com"), "King");
$u3 = new Utilisateur(new Profil("Poalo", "Pablo", "p.p@gmail.com"), "PP");
$u4 = new Utilisateur(new Profil("Valorys", "Vayant", "v.v@monbureau-numérique.fr"), "Pronote");

// $u1 sera le plus populaire
$u2->suivreUtilisateur($u1);
$u3->suivreUtilisateur($u1);
$u4->suivreUtilisateur($u1);
$u1->suivreUtilisateur($u1); // permet de voir qui ne peut pas s'auto-suivre

// $u2
$u3->suivreUtilisateur($u2);
$u4->suivreUtilisateur($u2);

// $u3
$u1->suivreUtilisateur($u3);

// $u4
$u3->suivreUtilisateur($u4);
$u2->suivreUtilisateur($u4);

echo <<<HTML
<h1>On doit avoir comme affichage</h1>
<ul>
<li>PP, nombre d'abonnés : 1</li>
<li>King, nombre d'abonnés : 2</li>
<li>Pronote, nombre d'abonnés : 2</li>
<li>King et Pronote permettent de vérifier que si 2 influenceurs ont le même nombre d'abonnés, l'ordre alphabétique des pseudos priment</li>
<li>Alice, nombre d'abonnés : 3 (si 4 l'auto-suivie fonctionne et ça ne doit pas être le cas)</li>
</ul>
<h1>Teste :</h1>
HTML;

// On crée la liste d'influenceur
$influenceur = [$u1, $u2, $u3, $u4];
echo RendererInfluenceur::rendererInfluenceur($influenceur);