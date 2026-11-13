<?php

namespace iutnc\touiteur\repository;

use InvalidArgumentException;
use iutnc\touiteur\classes\Profil;
use iutnc\touiteur\classes\Tag;
use iutnc\touiteur\classes\Touite;
use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\exception\BDException;
use PDO;
use PDOException;


/**
 * Classe permettant de faire communiquer notre application
 * avec la base de données
 */
class RepositoryBD extends Repository
{
// attributs
    private PDO $pdo; // connexion avec la base de données
    private bool $charge = false; // true quand les données ont déjà été chargées
    private array $utilisateurs = []; // liste des utilisateurs (pseudo => Utilisateur)
    private array $tags = []; // liste des tags (libelle => Tag)
    private array $touites = []; // liste des touites (idTouite => Touite)


    /**
     * Constructeur qui crée la connexion avec la base de données.
     * Les identifiants sont dans config/db.config.ini (pas sur git)
     */
    protected function __construct()
    {
        parent::__construct();

        $config = parse_ini_file(__DIR__ . '/../../config/db.config.ini');
        try {
            $this->pdo = new PDO(
                'mysql:host=' . $config['host'] . ';dbname=' . $config['database'] . ';charset=utf8mb4',
                $config['username'],
                $config['password']
            );
            // Les erreurs SQL lèvent des exceptions
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // On n'affiche pas le message de PDO à l'utilisateur (il contient le login)
            die('Erreur de connexion à la base de données');
        }
    }


    /**
     * Méthode charger qui charge les données à partir de la BD (une seule fois)
     * @return void
     * @throws \iutnc\touiteur\exception\LengthTouiteException erreur provenant de touite
     */
    private function charger(): void
    {
        // Déjà chargé : on ne recrée pas les objets
        if ($this->charge) return;
        $this->charge = true;

        // Utilisateurs
        $query = $this->pdo->query("select pseudo, nom, prenom, email from Profil");
        $query->setFetchMode(PDO::FETCH_ASSOC);
        while ($info = $query->fetch()) {
            $u = new Utilisateur(new Profil($info['nom'], $info['prenom'], $info['email']), $info['pseudo']);
            $this->utilisateurs[$info['pseudo']] = $u;
        }
        $query->closeCursor();

        // Tags
        $query = $this->pdo->query("select idTag, libelle, descriptionTag from Tag");
        $query->setFetchMode(PDO::FETCH_ASSOC);
        while ($info = $query->fetch()) {
            $tag = new Tag($info['libelle'], $info['descriptionTag'] ?? '');
            $tag->definirId((int) $info['idTag']);
            $this->tags[$info['libelle']] = $tag;
        }
        $query->closeCursor();

        // Touites (du plus ancien au plus récent) avec le chemin de leur image (null si pas d'image)
        $query = $this->pdo->query("select t.idTouite, t.pseudo, t.message, t.dateTouite, i.chemin from Touite t"
            . " left join Image i on i.idImage = t.idImage order by t.dateTouite, t.idTouite");
        $query->setFetchMode(PDO::FETCH_ASSOC);
        while ($info = $query->fetch()) {
            $auteur = $this->utilisateurs[$info['pseudo']]; // On récupère l'utilisateur/auteur
            $touite = new Touite($info['message'], $auteur, $info['dateTouite'], $info['chemin']);
            $touite->definirId((int) $info['idTouite']); // le touite garde l'id de la BDD
            $this->touites[$touite->id] = $touite;
            $auteur->ajouterTouite($touite); // On ajoute le touite à l'auteur
        }
        $query->closeCursor();

        // Notes des touites
        $query = $this->pdo->query("select pseudo, idTouite, valeur from Note");
        $query->setFetchMode(PDO::FETCH_ASSOC);
        while ($info = $query->fetch()) {
            $this->touites[(int) $info['idTouite']]->noter($info['pseudo'], (int) $info['valeur']);
        }
        $query->closeCursor();

        // Abonnes : pseudoAbonne suit pseudoSuivis
        $query = $this->pdo->query("select pseudoSuivis, pseudoAbonne from Abonnes");
        $query->setFetchMode(PDO::FETCH_ASSOC);
        while ($info = $query->fetch()) {
            $this->utilisateurs[$info['pseudoAbonne']]->suivreUtilisateur($this->utilisateurs[$info['pseudoSuivis']]);
        }
        $query->closeCursor();

        // AbonnesTag
        $query = $this->pdo->query("select a.pseudo, t.libelle from AbonnesTag a"
            . " inner join Tag t on t.idTag = a.idTag");
        $query->setFetchMode(PDO::FETCH_ASSOC);
        while ($info = $query->fetch()) {
            $this->utilisateurs[$info['pseudo']]->suivreTag($this->tags[$info['libelle']]);
        }
        $query->closeCursor();
    }


    /**
     * Methode getTouite pour avoir un Touite
     * @param mixed $id du touite qu'on veut
     * @return Touite le touite correspondant à l'id
     * @throws InvalidArgumentException si aucun touite n'a cet id
     */
    public function getTouite(mixed $id): Touite
    {
        $this->charger();
        if (isset($this->touites[$id])) return $this->touites[$id];
        throw new InvalidArgumentException("Aucun Touite avec cet id");
    }

    /**
     * Méthode getUtilisateur pour avoir un utilisateur
     * @param mixed $id pseudo de l'utilisateur qu'on veut
     * @return Utilisateur qu'on a demandé
     * @throws InvalidArgumentException si aucun utilisateur n'a ce pseudo
     */
    public function getUtilisateur(mixed $id): Utilisateur
    {
        $this->charger();
        if (isset($this->utilisateurs[$id])) return $this->utilisateurs[$id];
        throw new InvalidArgumentException("Aucun utilisateur n'a ce pseudo");
    }

    /**
     * Méthode getTag pour avoir un tag
     * @param mixed $id libellé du tag qu'on veut
     * @return Tag le tag demandé
     * @throws InvalidArgumentException si aucun tag n'a ce libellé
     */
    public function getTag(mixed $id): Tag
    {
        $this->charger();
        if (isset($this->tags[$id])) return $this->tags[$id];
        throw new InvalidArgumentException("Aucun tag n'a ce libellé");
    }

    /**
     * Methode getTouites qui retourne la liste des touites
     * @return array la liste des touites
     */
    public function getTouites(): array
    {
        $this->charger();
        return $this->touites;
    }

    /**
     * Méthode getUtilisateurs pour avoir la liste des utilisateurs
     * @return array la liste des utilisateurs
     */
    public function getUtilisateurs(): array
    {
        $this->charger();
        return $this->utilisateurs;
    }

    /**
     * Méthode getTags pour avoir la liste des tags
     * @return array la liste des tags
     */
    public function getTags(): array
    {
        $this->charger();
        return $this->tags;
    }


    /**
     * Méthode stocker qui enregistre un Touite, un Utilisateur ou un Tag dans la BDD
     * @param mixed $data l'objet à stocker
     * @return void
     * @throws InvalidArgumentException si l'objet ne peut pas être stocké
     */
    public function stocker(mixed $data): void
    {
        $this->charger();

        if ($data instanceof Touite) {
            $this->stockerTouite($data);
        } elseif ($data instanceof Utilisateur) {
            $this->stockerUtilisateur($data);
        } elseif ($data instanceof Tag) {
            $this->stockerTag($data);
        } else {
            throw new InvalidArgumentException("Le type de donnée entrée ne peut pas être stocké.");
        }
    }

    /**
     * Méthode stockerUtilisateur qui stocke un utilisateur dans la BDD
     * @param Utilisateur $u utilisateur à stocker
     * @return void
     */
    private function stockerUtilisateur(Utilisateur $u): void
    {
        // Déjà dans la base alors on ne le stocke pas
        if (isset($this->utilisateurs[$u->pseudo])) return;

        // Sinon on le stocke
        $query = $this->pdo->prepare("insert into Profil (pseudo, prenom, nom, email) values (?,?,?,?)");
        $query->execute(array($u->pseudo, $u->profil->prenom, $u->profil->nom, $u->profil->email));
        $query->closeCursor();

        $this->utilisateurs[$u->pseudo] = $u;
    }

    /**
     * Méthode stockerTag qui stocke un tag dans la BDD s'il n'y est pas déjà
     * @param Tag $tag tag à stocker
     * @return int l'idTag du tag dans la BDD
     * @throws BDException si le libellé est trop long pour la colonne (20 caractères)
     */
    private function stockerTag(Tag $tag): int
    {
        // Déjà dans la base : on reprend son id
        if ($tag->idTag !== null) return $tag->idTag;

        $libelle = $tag->libelleTag;

        // Un tag avec ce libellé est déjà dans la base (ex : #cafe extrait d'un nouveau touite)
        if (isset($this->tags[$libelle])) {
            $id = $this->tags[$libelle]->idTag;
            $tag->definirId($id);
            return $id;
        }

        if (mb_strlen($libelle) > 20) {
            throw new BDException("Le tag #$libelle est trop long (20 caractères max).");
        }

        // Sinon on le stocke, et la BDD lui donne son id
        $query = $this->pdo->prepare("insert into Tag (libelle, descriptionTag) values (?,?)");
        $query->execute(array($libelle, $tag->descriptionTag));
        $query->closeCursor();

        $tag->definirId((int) $this->pdo->lastInsertId());
        $this->tags[$libelle] = $tag;
        return $tag->idTag;
    }

    /**
     * Méthode stockerTouite qui stocke un touite et ses tags dans la BDD
     * @param Touite $touite touite à stocker
     * @return void
     * @throws BDException si un trigger refuse le touite (plus de 50 touites aujourd'hui)
     */
    private function stockerTouite(Touite $touite): void
    {
        // Son image s'il en a une (la BDD lui donne son id)
        $idImage = null;
        if ($touite->image !== null) {
            $query = $this->pdo->prepare("insert into Image (chemin) values (?)");
            $query->execute(array($touite->image));
            $query->closeCursor();
            $idImage = (int) $this->pdo->lastInsertId();
        }

        // Le touite (nbTouites est mis à jour par le trigger trig_ajoutTouite)
        try {
            $query = $this->pdo->prepare("insert into Touite (pseudo, message, dateTouite, idImage) values (?,?,?,?)");
            $query->execute(array(
                $touite->auteur->pseudo,
                $touite->message,
                date('Y-m-d H:i:s', strtotime($touite->date)), // format DATETIME de MySQL
                $idImage
            ));
            $query->closeCursor();
        } catch (PDOException $e) {
            // Erreur levée par un trigger (anti-spam) : on la remonte avec son message
            if ($e->errorInfo[0] === '45000') throw new BDException($e->errorInfo[2]);
            throw $e;
        }

        // Le touite prend l'id donné par la BDD
        $idTouite = (int) $this->pdo->lastInsertId();
        $touite->definirId($idTouite);
        $this->touites[$idTouite] = $touite;

        // Ses tags (créés s'ils n'existent pas) + table de liaison TouiteTag
        $query = $this->pdo->prepare("insert into TouiteTag (idTouite, idTag) values (?,?)");
        foreach ($touite->tags as $tag) {
            // Un tag trop long reste dans le message mais n'est pas enregistré comme tag
            if (mb_strlen($tag->libelleTag) > 20) continue;
            $query->execute(array($idTouite, $this->stockerTag($tag)));
        }
        $query->closeCursor();
    }


    /**
     * Methode suivreUtilisateur qui fait qu'un abonné suit un autre utilisateur
     * @param Utilisateur $suiveur utilisateur qui va suivre quelqu'un
     * @param Utilisateur $suivi utilisateur qui sera suivi
     * @return void
     * @throws BDException si le trigger refuse (se suivre soi-même / même email)
     */
    public function suivreUtilisateur(Utilisateur $suiveur, Utilisateur $suivi): void
    {
        $this->charger();

        // Déjà abonné : rien à faire (sinon erreur de clé primaire)
        if (in_array($suivi, $suiveur->utilisateursSuivis, true)) return;

        try {
            $query = $this->pdo->prepare("insert into Abonnes (pseudoSuivis, pseudoAbonne) values (?,?)");
            $query->execute(array($suivi->pseudo, $suiveur->pseudo));
            $query->closeCursor();
        } catch (PDOException $e) {
            if ($e->errorInfo[0] === '45000') throw new BDException($e->errorInfo[2]);
            throw $e;
        }

        $suiveur->suivreUtilisateur($suivi);
    }

    /**
     * Methode suivreTag qui abonne un utilisateur à un tag
     * @param Utilisateur $utilisateur utilisateur qui s'abonne
     * @param Tag $tag tag suivi
     * @return void
     */
    public function suivreTag(Utilisateur $utilisateur, Tag $tag): void
    {
        $this->charger();

        // Déjà abonné : rien à faire
        foreach ($utilisateur->tagsSuivis as $t) {
            if ($t->libelleTag === $tag->libelleTag) return;
        }

        $query = $this->pdo->prepare("insert into AbonnesTag (pseudo, idTag) values (?,?)");
        $query->execute(array($utilisateur->pseudo, $this->stockerTag($tag)));
        $query->closeCursor();

        $utilisateur->suivreTag($tag);
    }

    /**
     * Methode noterTouite qui enregistre la note d'un utilisateur sur un touite
     * @param Utilisateur $utilisateur utilisateur qui note
     * @param Touite $touite touite noté
     * @param int $valeur +1 si approuvé, -1 si rejeté
     * @return bool true si la note a été ajoutée, false si l'utilisateur avait déjà noté le touite
     */
    public function noterTouite(Utilisateur $utilisateur, Touite $touite, int $valeur): bool
    {
        $this->charger();

        // Déjà noté : rien à faire (sinon erreur de clé primaire)
        if (!$touite->noter($utilisateur->pseudo, $valeur)) return false;

        $query = $this->pdo->prepare("insert into Note (pseudo, idTouite, valeur) values (?,?,?)");
        $query->execute(array($utilisateur->pseudo, $touite->id, $valeur));
        $query->closeCursor();

        return true;
    }

}