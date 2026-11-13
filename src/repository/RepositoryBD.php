<?php

namespace iutnc\touiteur\repository;

use iutnc\touiteur\classes\Utilisateur;
use PDO;

/**
 * Classe permettant de faire communiquer notre application
 * avec la base de données
 */
class RepositoryBD extends Repository{
// attributs
private PDO $pdo;
private array $utilisateur =[];
private array $tags = [];


// TODO connection

private  function charger(){
    $res = $this->pdo->query("Select pseudo, nom, prenomn, email from utilisateur");

    // Utilisateurs
    foreach ($res as $info){
        $u = new Utilisateur(new Profil($info['nom'], $info['prenom'], $info['email']), $info['pseudo']);
        $this->utilisateur[$info['pseudo']] = $u;
    }

    // Tags
    $res = $this->pdo->query("select id_tag, libelle, description from tag");
    foreach($res as $info){
        $this->tags[$info['libelle']] = new Tag($info['Libelle'], $info['description']);
    }
}

}
