<?php

require_once __DIR__ . '../../vendor/autoload.php';

use \iutnc\touiteur\repository\RepoDev;

$repo = RepoDev::getInstance();
$file = __DIR__ . "/../config/dev_dummy_data.json";

$repo->chargerDonnees($file);

// Vérification visuelle
$users = $repo->getUtilisateurs();
$tags  = $repo->getTags();
$touites = $repo->getTouites();

echo "Donnée obtenues :\n";
echo "Utilisateurs (" . count($users) . ")\n";
foreach ($users as $pseudo => $u) {
    echo "  - $pseudo (nom:{$u->profil->nom} prenom:{$u->profil->prenom} email:{$u->profil->email})\n";

    echo "    Utilisateurs suivis : ";
    foreach ($u->utilisateursSuivis as $utilisateurSuivi) {
        echo $utilisateurSuivi->pseudo . " ";
    }
    echo "\n";

    echo "    Tags suivis : ";
    foreach ($u->tagsSuivis as $tagSuivi) {
        echo "#" . $tagSuivi->libelleTag . " ";
    }
    echo "\n";

    echo "    Nombre de touites publies : " . count($u->touitesPublies) . "\n";
}

echo "\nTags (" . count($tags) . ")\n";
foreach ($tags as $libelle => $tag) {
    echo "  - #$libelle : {$tag->descriptionTag}\n";
}

echo "\nTouites (" . count($touites) . ")\n";
foreach ($touites as $id => $t) {
    echo "  [$id] {$t->auteur->pseudo} : {$t->message}\n";
}

echo "\n\nFichier config :\n";
echo file_get_contents($file);