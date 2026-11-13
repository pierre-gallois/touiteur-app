<?php

namespace iutnc\touiteur\repository;

use http\Exception\InvalidArgumentException;
use iutnc\touiteur\classes\Touite;
use iutnc\touiteur\classes\Utilisateur;
use PDO;
use iutnc\touiteur\classes\tag;


/**
 * Classe permettant de faire communiquer notre application
 * avec la base de données
 */
class RepositoryBD extends Repository
{
// attributs
    private PDO $pdo; // connection avec la base de donnée
    private array $utilisateurs = []; // liste des utilisateurs
    private array $tags = []; // liste des tags
    private array $touites = []; // liste des touites


// TODO connection

    /**
     * Méthode charger qui charge les donénes à partir de la BD
     * @return void
     * @throws \iutnc\touiteur\exception\LengthTouiteException erreur provenant de toutie
     */
    private function charger(): void
    {
        $res = $this->pdo->query("Select pseudo, nom, prenomn, email from utilisateur");

        // Utilisateurs
        foreach ($res as $info) {
            $u = new Utilisateur(new Profil($info['nom'], $info['prenom'], $info['email']), $info['pseudo']);
            $this->utilisateurs[$info['pseudo']] = $u;
        }

        // Tags
        $res = $this->pdo->query("select id_tag, libelle, description from tag");
        foreach ($res as $info) {
            $this->tags[$info['libelle']] = new Tag($info['Libelle'], $info['description']);
        }

        // Touite
        $res = $this->pdo->query("select idTouite, pseudo, message, dateTouite, idImage from touite");
        foreach ($res as $info) {
            $auteur = $this->utilisateurs[$info['pseudo']]; // On récupère l'utilisateur/auteur
            $touite = new Touite($info['message'], $auteur, $info['dateTouite'], $info['idTouite'], $info['idImage']);
            $this->touites[$info['idTouite']] = $touite;
            $auteur->ajouterTouite($touite); // On ajoute le touite à l'auteur
        }

        // Abonnes
        $res = $this->pdo->query("select pseudoSuivis, pseudoAbonnes from abonnes");
        foreach ($res as $info) {
            $this->utilisateurs[$info['pseudoAbonnes']]->suivreUtilisateur($this->$info['pseudoSuivis']);
        }

        // AbonnesTag
        $res = $this->pdo->query("select a.pseudo, a.idTag, t.libelle from AbonnesTag a "
            . " inner join tag t on t.idTag = a.idTag");
        foreach ($res as $info) {
            $this->utilisateurs[$info['pseudo']->suivreTag($this->tags[$info['libelle']]);
        }

    }

    /**
     * Methode getTouite pour avoir un Touite
     * @param int $id du touite qu'on veut
     * @return Touite le touite correspondant à l'id
     * @throws \InvalidArgumentException si aucun touite n'a cet id
     */
    public function getTouite(int $id): Touite
    {
        $this->charger(); // On charge la BDD pour récupérer les dernières données
        if (isset($this->touites[$id])) return $this->touites[$id];
        throw new InvalidArgumentException("Aucun Touite avec cet id");
    }

    /**
     * Méthode getUtilisateur pour avoir un utilisateur
     * @param string $pseudo de l'utilisateur qu'on veut
     * @return Utilisateur qu'on a demandé
     * @throws \InvalidArgumentException si aucun utilisateur n'a ce pseudo
     */
    public function getUtilisateur(string $pseudo): Utilisateur
    {
        $this->charger(); // On charge la BDD pour récupérer les dernières données
        if (isset($this->utilisateurs[$pseudo])) return $this->utilisateurs[$pseudo];
        throw new InvalidArgumentException("Aucun utilisateur n'a ce pseudo");
    }

    /**
     * Methode getTouites qui retourne la liste des touites
     * @return array la liste des touites
     * @throws \iutnc\touiteur\exception\LengthTouiteException erreur que peuvent générer les touites
     */
    public function getTouites(): array
    {
        $this->charger(); // On charge la BDD pour récupérer les dernières données
        return $this->touites;
    }

    /**
     * Méthode getUtilisateurs pour avoir la liste des utilisateurs
     * @return array la liste des utilisateur
     * @throws \iutnc\touiteur\exception\LengthTouiteException
     */
    public function getUtilisateurs(): array
    {
        $this->charger(); // On charge la BDD pour récupérer les dernières données
        return $this->utilisateurs;
    }

    /**
     * Méthode getTags pour avoir la liste des tags
     * @return array la liste des tags
     * @throws \iutnc\touiteur\exception\LengthTouiteException
     */
    public function getTags(): array
    {
        $this->charger(); // On charge la BDD pour récupérer les dernières données
        return $this->tags;
    }

    /**
     * Méthode stockerUtilisateur qui stocke un utilisateur dans la BDD
     * @param Utilisateur $u utilisateur à stocker
     * @return void
     */
    private function stockerUtilisateur(Utilisateur $u) : void
    {
        // Déjà dans la base alors on le stocke pas
        if (isset($this->utilisateurs[$u->['pseudo']])) return;

        // Sinon on le stocke
        $this->pdo->prepare("insert into profil (pseudo, prenom, nom, email) values (?,?,?,?)");
        $this->pdo->exec([$u->pseudo, $u->prenom, $u->nom, $u->email]);
        $this->utilisateurs[$u->pseudo] = $u;
    }

    /**
     * Methode stockerTag qui stocke un tag dans la BDD
     * @param tag $t tag à stocker
     * @return int id du tag
     */
    public function stockerTag(Tag $t) : int{

        // Déjà dans la base
        if($t->idTag !== null) return $t->idTag;

        $libelle = $t->libelleTag;

        // Un tag avec ce libelle est déjà dans la table
        if(isset($this->tags[$libelle])){
            $id = $this->tags[$libelle]->tag;
            $t->definirId($id);
            return $id;
        }

        // Limite définit par la BD
        if (mb_strlen($libelle) > 20) {
            throw new InvalidArgumentException("Le tag #$libelle est trop long.");
        }

        // Sinon on le stocke
        $query = $this->pdo->prepare("insert into Tag (libelle, descriptionTag) values (?,?)");
        $query->execute(array($libelle, $tag->descriptionTag));
        $query->closeCursor();

        $tag->definirId((int) $this->pdo->lastInsertId());
        $this->tags[$libelle] = $tag;
        return $tag->idTag;
    }

    /**
     * Methode suivreUtilisateur qui fait qu'un abonné suit un autre utilisateur
     * @param Utilisateur $abonne utilisateur qui va suivre quelqu'un
     * @param Utilisateur $suivi utilisateur qui sera suivi
     * @return void
     */
    public function suivreUtilisateur(Utilisateur $abonne, Utilisateur $suivi){
        $this->pdo->prepare("insert into Abonnes values (?,?)");
        $this->pdo->exec([$suivi->pseudo, $abonne->pseudo]);
        $abonne->suivreUtilisateur($suivi);
    }

}
