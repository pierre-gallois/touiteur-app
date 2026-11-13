<?php

namespace iutnc\touiteur\repository;

use iutnc\touiteur\classes\Touite;
use iutnc\touiteur\classes\Utilisateur;
use PDO;

/**
 * Classe permettant de faire communiquer notre application
 * avec la base de données
 */
class RepositoryBD extends Repository
{
// attributs
    private PDO $pdo; // connection avec la base de donnée
    private array $utilisateur = []; // liste des utilisateurs
    private array $tags = []; // liste des tags
    private array $touites = []; // liste des touites


// TODO connection

    private function charger(): void
    {
        $res = $this->pdo->query("Select pseudo, nom, prenomn, email from utilisateur");

        // Utilisateurs
        foreach ($res as $info) {
            $u = new Utilisateur(new Profil($info['nom'], $info['prenom'], $info['email']), $info['pseudo']);
            $this->utilisateur[$info['pseudo']] = $u;
        }

        // Tags
        $res = $this->pdo->query("select id_tag, libelle, description from tag");
        foreach ($res as $info) {
            $this->tags[$info['libelle']] = new Tag($info['Libelle'], $info['description']);
        }

        // Touite
        $res = $this->pdo->query("select idTouite, pseudo, message, dateTouite, idImage from touite");
        foreach ($res as $info) {
            $auteur = $this->utilisateur[$info['pseudo']]; // On récupère l'utilisateur/auteur
            $touite = new Touite($info['message'], $auteur, $info['dateTouite'], $info['idTouite'], $info['idImage']);
            $this->touites[$info['idTouite']] = $touite;
            $auteur->ajouterTouite($touite); // On ajoute le touite à l'auteur
        }

        // Abonnes
        $res = $this->pdo->query("select pseudoSuivis, pseudoAbonnes from abonnes");
        foreach ($res as $info) {
            $this->utilisateur[$info['pseudoAbonnes']]->suivreUtilisateur($this->$info['pseudoSuivis']);
        }

        // AbonnesTag
        $res = $this->pdo->query("select a.pseudo, a.idTag, t.libelle from AbonnesTag a "
            . " inner join tag t on t.idTag = a.idTag");
        foreach ($res as $info) {
            $this->utilisateurs[$info['pseudo']->suivreTag($this->tags[$info['libelle']]);
        }



    }

}
