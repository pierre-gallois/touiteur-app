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
use PDOStatement;

class RepositoryBD extends Repository
{
    private PDO $pdo;

    private bool $charge = false;

    private array $utilisateurs = [];
    private array $tags = [];
    private array $touites = [];

    /**
     * Constructeur : connexion à la base Oracle.
     */
    protected function __construct()
    {
        parent::__construct();

        $config = parse_ini_file(__DIR__ . '/../../config/db.config.ini');

        try {
            $this->pdo = new PDO(
                'oci:dbname=//' . $config['host'] . ':1521/' . $config['database'] . ';charset=AL32UTF8',
                $config['username'],
                $config['password']
            );

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            die('Erreur de connexion à la base de données');
        }
    }

    /**
     * Charge les données de la base une seule fois.
     */
    private function charger(): void
    {
        if ($this->charge) {
            return;
        }

        // --------------------------------
        // UTILISATEURS
        // --------------------------------

        $query = $this->pdo->query(
            "SELECT pseudo, nom, prenom, email
             FROM Profil"
        );

        while ($info = $query->fetch()) {
            $profil = new Profil(
                $info['NOM'],
                $info['PRENOM'],
                $info['EMAIL']
            );

            $utilisateur = new Utilisateur(
                $profil,
                $info['PSEUDO']
            );

            $this->utilisateurs[$info['PSEUDO']] = $utilisateur;
        }

        $query->closeCursor();

        // --------------------------------
        // TAGS
        // --------------------------------

        $query = $this->pdo->query(
            "SELECT idTag, libelle, descriptionTag
             FROM Tag"
        );

        while ($info = $query->fetch()) {
            $tag = new Tag(
                $info['LIBELLE'],
                $info['DESCRIPTIONTAG'] ?? ''
            );

            $tag->definirId((int) $info['IDTAG']);

            $this->tags[$info['LIBELLE']] = $tag;
        }

        $query->closeCursor();

        // --------------------------------
        // TOUITES
        // --------------------------------

        $query = $this->pdo->query(
            "SELECT idTouite,
                    pseudo,
                    message,
                    TO_CHAR(dateTouite, 'YYYY-MM-DD HH24:MI:SS') AS dateTouite,
                    idImage
             FROM Touite
             ORDER BY dateTouite, idTouite"
        );

        while ($info = $query->fetch()) {

            if (!isset($this->utilisateurs[$info['PSEUDO']])) {
                continue;
            }

            $auteur = $this->utilisateurs[$info['PSEUDO']];

            $touite = new Touite(
                $info['MESSAGE'],
                $auteur,
                $info['DATETOUITE'],
                (int) $info['IDTOUITE'],
                $info['IDIMAGE'] === null ? null : (int) $info['IDIMAGE']
            );

            $this->touites[$touite->id] = $touite;

            // Ajoute le touite à son auteur.
            // Le compteur du profil est ainsi reconstruit en mémoire.
            $auteur->ajouterTouite($touite);
        }

        $query->closeCursor();

        // --------------------------------
        // ABONNEMENTS UTILISATEURS
        // --------------------------------

        // pseudoAbonne suit pseudoSuivis
        $query = $this->pdo->query(
            "SELECT pseudoSuivis, pseudoAbonne
             FROM Abonnes"
        );

        while ($info = $query->fetch()) {

            $abonne = $info['PSEUDOABONNE'];
            $suivi = $info['PSEUDOSUIVIS'];

            if (
                isset($this->utilisateurs[$abonne]) &&
                isset($this->utilisateurs[$suivi])
            ) {
                $this->utilisateurs[$abonne]->suivreUtilisateur(
                    $this->utilisateurs[$suivi]
                );
            }
        }

        $query->closeCursor();

        // --------------------------------
        // ABONNEMENTS AUX TAGS
        // --------------------------------

        $query = $this->pdo->query(
            "SELECT a.pseudo, t.libelle
             FROM AbonnesTag a
             INNER JOIN Tag t ON t.idTag = a.idTag"
        );

        while ($info = $query->fetch()) {

            $pseudo = $info['PSEUDO'];
            $libelle = $info['LIBELLE'];

            if (
                isset($this->utilisateurs[$pseudo]) &&
                isset($this->tags[$libelle])
            ) {
                $this->utilisateurs[$pseudo]->suivreTag(
                    $this->tags[$libelle]
                );
            }
        }

        $query->closeCursor();

        $this->charge = true;
    }

    /**
     * Retourne un touite à partir de son identifiant.
     */
    public function getTouite(mixed $id): Touite
    {
        $this->charger();

        if (isset($this->touites[$id])) {
            return $this->touites[$id];
        }

        throw new InvalidArgumentException("Aucun touite avec cet identifiant.");
    }

    /**
     * Retourne un utilisateur à partir de son pseudo.
     */
    public function getUtilisateur(mixed $id): Utilisateur
    {
        $this->charger();

        if (isset($this->utilisateurs[$id])) {
            return $this->utilisateurs[$id];
        }

        throw new InvalidArgumentException("Aucun utilisateur avec ce pseudo.");
    }

    /**
     * Retourne un tag à partir de son libellé.
     */
    public function getTag(mixed $id): Tag
    {
        $this->charger();

        if (isset($this->tags[$id])) {
            return $this->tags[$id];
        }

        throw new InvalidArgumentException("Aucun tag avec ce libellé.");
    }

    /**
     * Retourne tous les touites.
     */
    public function getTouites(): array
    {
        $this->charger();

        return $this->touites;
    }

    /**
     * Retourne tous les utilisateurs.
     */
    public function getUtilisateurs(): array
    {
        $this->charger();

        return $this->utilisateurs;
    }

    /**
     * Retourne tous les tags.
     */
    public function getTags(): array
    {
        $this->charger();

        return $this->tags;
    }

    /**
     * Enregistre un Touite, un Utilisateur ou un Tag.
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
            throw new InvalidArgumentException(
                "Le type de donnée ne peut pas être stocké."
            );
        }
    }

    /**
     * Enregistre un utilisateur.
     */
    private function stockerUtilisateur(Utilisateur $u): void
    {
        if (isset($this->utilisateurs[$u->pseudo])) {
            return;
        }

        $sql = "INSERT INTO Profil (pseudo, prenom, nom, email)
                VALUES (:pseudo, :prenom, :nom, :email)";

        $this->executer($sql, [
            ':pseudo' => $u->pseudo,
            ':prenom' => $u->profil->prenom,
            ':nom' => $u->profil->nom,
            ':email' => $u->profil->email
        ]);

        $this->utilisateurs[$u->pseudo] = $u;
    }

    /**
     * Enregistre un tag s'il n'existe pas.
     * La base attribue son idTag.
     *
     * @return int identifiant du tag
     */
    private function stockerTag(Tag $tag): int
    {
        $libelle = $tag->libelleTag;

        // Le tag possède déjà un identifiant.
        if ($tag->idTag !== null) {
            return $tag->idTag;
        }

        // Le tag existe déjà en mémoire.
        if (isset($this->tags[$libelle])) {
            $id = $this->tags[$libelle]->idTag;
            $tag->definirId($id);

            return $id;
        }

        if (mb_strlen($libelle, 'UTF-8') > 20) {
            throw new BDException(
                "Le tag #$libelle est trop long (20 caractères maximum)."
            );
        }

        // On laisse la base attribuer idTag.
        $sql = "INSERT INTO Tag (libelle, descriptionTag)
                VALUES (:libelle, :description)";

        $this->executer($sql, [
            ':libelle' => $libelle,
            ':description' => $tag->descriptionTag
        ]);

        // On récupère l'identifiant attribué par la base.
        $sql = "SELECT idTag FROM Tag WHERE libelle = :libelle";

        $query = $this->pdo->prepare($sql);
        $query->execute([':libelle' => $libelle]);

        $id = $query->fetchColumn();
        $query->closeCursor();

        if ($id === false) {
            throw new BDException("Impossible de récupérer l'identifiant du tag.");
        }

        $id = (int) $id;

        $tag->definirId($id);
        $this->tags[$libelle] = $tag;

        return $id;
    }

    /**
     * Enregistre un touite et ses tags.
     */
    private function stockerTouite(Touite $touite): void
    {
        // L'idTouite est déjà fourni par le touite.
        $idTouite = $touite->id;

        if (isset($this->touites[$idTouite])) {
            return;
        }

        $sql = "INSERT INTO Touite
                    (idTouite, pseudo, message, dateTouite, idImage)
                VALUES
                    (:id, :pseudo, :message,
                     TO_DATE(:date, 'YYYY-MM-DD HH24:MI:SS'),
                     :image)";

        $date = $this->formaterDate($touite->date);

        $this->executer($sql, [
            ':id' => $idTouite,
            ':pseudo' => $touite->auteur->pseudo,
            ':message' => $touite->message,
            ':date' => $date,
            ':image' => $touite->idImage
        ]);

        $this->touites[$idTouite] = $touite;

        // Enregistrement des tags du touite.
        foreach ($touite->tags as $tag) {

            // Un tag trop long n'est pas enregistré.
            if (mb_strlen($tag->libelleTag, 'UTF-8') > 20) {
                continue;
            }

            $idTag = $this->stockerTag($tag);

            $sql = "INSERT INTO TouiteTag (idTouite, idTag)
                    VALUES (:idTouite, :idTag)";

            $this->executer($sql, [
                ':idTouite' => $idTouite,
                ':idTag' => $idTag
            ]);
        }
    }

    /**
     * Un utilisateur suit un autre utilisateur.
     */
    public function suivreUtilisateur(
        Utilisateur $suiveur,
        Utilisateur $suivi
    ): void {
        $this->charger();

        // Évite de suivre deux fois le même utilisateur.
        if (in_array($suivi, $suiveur->utilisateursSuivis, true)) {
            return;
        }

        $sql = "INSERT INTO Abonnes (pseudoSuivis, pseudoAbonne)
                VALUES (:suivi, :suiveur)";

        $this->executer($sql, [
            ':suivi' => $suivi->pseudo,
            ':suiveur' => $suiveur->pseudo
        ]);

        // Mise à jour des objets en mémoire.
        $suiveur->suivreUtilisateur($suivi);
    }

    /**
     * Un utilisateur s'abonne à un tag.
     */
    public function suivreTag(Utilisateur $utilisateur, Tag $tag): void
    {
        $this->charger();

        // Évite les doublons.
        foreach ($utilisateur->tagsSuivis as $t) {
            if ($t->libelleTag === $tag->libelleTag) {
                return;
            }
        }

        // On enregistre le tag s'il n'existe pas encore.
        $idTag = $this->stockerTag($tag);

        $sql = "INSERT INTO AbonnesTag (pseudo, idTag)
                VALUES (:pseudo, :idTag)";

        $this->executer($sql, [
            ':pseudo' => $utilisateur->pseudo,
            ':idTag' => $idTag
        ]);

        $utilisateur->suivreTag($tag);
    }

    /**
     * Exécute une requête préparée.
     * Transforme les erreurs des triggers Oracle en BDException.
     */
    private function executer(string $sql, array $params): PDOStatement
    {
        try {
            $query = $this->pdo->prepare($sql);
            $query->execute($params);

            return $query;

        } catch (PDOException $e) {

            $message = $e->getMessage();

            if (str_contains($message, 'ORA-20001')) {
                throw new BDException(
                    "Un utilisateur ne peut pas se suivre lui-même ou un utilisateur ayant le même email.",
                    0,
                    $e
                );
            }

            if (str_contains($message, 'ORA-20003')) {
                throw new BDException(
                    "Vous avez atteint la limite de 50 touites par jour.",
                    0,
                    $e
                );
            }

            throw $e;
        }
    }

    /**
     * Convertit une date au format attendu par Oracle.
     */
    private function formaterDate(string $date): string
    {
        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d',
            'd.m.Y H:i:s',
            'd.m.Y',
            'd/m/Y'
        ];

        foreach ($formats as $format) {
            $d = \DateTime::createFromFormat('!' . $format, $date);

            if ($d !== false) {
                return $d->format('Y-m-d H:i:s');
            }
        }

        $timestamp = strtotime($date);

        if ($timestamp === false) {
            throw new InvalidArgumentException("Date invalide : $date");
        }

        return date('Y-m-d H:i:s', $timestamp);
    }
}
