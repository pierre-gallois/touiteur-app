<?php

namespace iutnc\touiteur\repository;

use iutnc\touiteur\classes\Profil;
use iutnc\touiteur\classes\Touite;
use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\classes\Tag;
use PDO;
use InvalidArgumentException;

/**
 * Classe permettant de faire communiquer l'application
 * avec la base de données.
 */
class RepositoryBD extends Repository
{

    private PDO $pdo;
    private array $utilisateurs = [];
    private array $tags = [];
    private array $touites = [];
    private array $idTags = [];
    private bool $charge = false;

    /**
     * Constructeur
     */
    public function __construct()
    {
        parent::__construct();
        $this->pdo = ConnectionFactory::makeConnection();
    }

    /**
     * Charge les données depuis la base de données.
     */
    private function charger(): void
    {
        if ($this->charge) return;

        $this->charge = true;

        // Utilisateurs
        $res = $this->pdo->query(
            "SELECT pseudo, nom, prenom, email FROM Profil"
        );

        foreach ($res as $info) {
            $u = new Utilisateur(
                new Profil(
                    $info['nom'],
                    $info['prenom'],
                    $info['email']
                ),
                $info['pseudo']
            );

            $this->utilisateurs[$info['pseudo']] = $u;
        }

        // Tags
        $res = $this->pdo->query(
            "SELECT idTag, libelle, descriptionTag FROM Tag"
        );

        foreach ($res as $info) {
            $tag = new Tag(
                $info['libelle'],
                $info['descriptionTag']
            );

            $this->tags[$info['libelle']] = $tag;
            $this->idTags[$info['libelle']] = (int) $info['idTag'];
        }

        // Touites
        $res = $this->pdo->query(
            "SELECT idTouite, pseudo, message, dateTouite, idImage FROM Touite"
        );

        foreach ($res as $info) {
            $auteur = $this->utilisateurs[$info['pseudo']];

            $touite = new Touite(
                $info['message'],
                $auteur,
                $info['dateTouite'],
                (int) $info['idTouite'],
                (int) $info['idImage']
            );

            $this->touites[$info['idTouite']] = $touite;
            $auteur->ajouterTouite($touite);
        }

        // Abonnements entre utilisateurs
        $res = $this->pdo->query(
            "SELECT pseudoSuivis, pseudoAbonne FROM Abonnes"
        );

        foreach ($res as $info) {
            $abonne = $this->utilisateurs[$info['pseudoAbonne']];
            $suivi = $this->utilisateurs[$info['pseudoSuivis']];

            $abonne->suivreUtilisateur($suivi);
        }

        // Abonnements aux tags
        $res = $this->pdo->query(
            "SELECT pseudo, idTag FROM AbonnesTag"
        );

        foreach ($res as $info) {
            $pseudo = $info['pseudo'];
            $idTag = (int) $info['idTag'];

            $libelle = array_search($idTag, $this->idTags, true);

            if ($libelle !== false) {
                $this->utilisateurs[$pseudo]->suivreTag(
                    $this->tags[$libelle]
                );
            }
        }
    }

    /**
     * Récupère un touite avec son identifiant.
     */
    public function getTouite(int $id): Touite
    {
        $this->charger();

        if (isset($this->touites[$id])) {
            return $this->touites[$id];
        }

        throw new InvalidArgumentException("Aucun touite avec cet id.");
    }

    /**
     * Récupère un utilisateur avec son pseudo.
     */
    public function getUtilisateur(string $pseudo): Utilisateur
    {
        $this->charger();

        if (isset($this->utilisateurs[$pseudo])) {
            return $this->utilisateurs[$pseudo];
        }

        throw new InvalidArgumentException("Aucun utilisateur avec ce pseudo.");
    }

    /**
     * Récupère un tag avec son libellé.
     */
    public function getTag(string $libelle): Tag
    {
        $this->charger();

        if (isset($this->tags[$libelle])) {
            return $this->tags[$libelle];
        }

        throw new InvalidArgumentException("Aucun tag avec ce libellé.");
    }

    /**
     * Récupère tous les touites.
     */
    public function getTouites(): array
    {
        $this->charger();
        return $this->touites;
    }

    /**
     * Récupère tous les utilisateurs.
     */
    public function getUtilisateurs(): array
    {
        $this->charger();
        return $this->utilisateurs;
    }

    /**
     * Récupère tous les tags.
     */
    public function getTags(): array
    {
        $this->charger();
        return $this->tags;
    }

    /**
     * Stocke un utilisateur dans la base de données.
     */
    private function stockerUtilisateur(Utilisateur $u): void
    {
        if (isset($this->utilisateurs[$u->pseudo])) return;

        $requete = $this->pdo->prepare(
            "INSERT INTO Profil (pseudo, prenom, nom, email)
             VALUES (?, ?, ?, ?)"
        );

        $requete->execute([
            $u->pseudo,
            $u->profil->prenom,
            $u->profil->nom,
            $u->profil->email
        ]);

        $this->utilisateurs[$u->pseudo] = $u;
    }

    /**
     * Stocke un tag dans la base de données.
     */
    public function stockerTag(Tag $t): int
    {
        $this->charger();

        $libelle = $t->libelleTag;

        // Le tag existe déjà
        if (isset($this->idTags[$libelle])) {
            return $this->idTags[$libelle];
        }

        // Vérification de la longueur
        if (mb_strlen($libelle, 'UTF-8') > 20) {
            throw new InvalidArgumentException("Le tag est trop long.");
        }

        $requete = $this->pdo->prepare(
            "INSERT INTO Tag (libelle, descriptionTag)
             VALUES (?, ?)"
        );

        $requete->execute([
            $libelle,
            $t->descriptionTag
        ]);

        $id = (int) $this->pdo->lastInsertId();

        $this->tags[$libelle] = $t;
        $this->idTags[$libelle] = $id;

        return $id;
    }

    /**
     * Stocke un touite et ses tags dans la base de données.
     */
    private function stockerTouite(Touite $t): void
    {
        $this->charger();

        $pseudo = $t->auteur->pseudo;

        if (!isset($this->utilisateurs[$pseudo])) {
            throw new InvalidArgumentException(
                "L'auteur du touite n'existe pas dans la base."
            );
        }

        try {
            $this->pdo->beginTransaction();

            // Insertion du touite
            $requete = $this->pdo->prepare(
                "INSERT INTO Touite (pseudo, message, dateTouite, idImage)
                 VALUES (?, ?, ?, ?)"
            );

            $requete->execute([
                $pseudo,
                $t->message,
                $t->date,
                $t->idImage
            ]);

            $idTouite = (int) $this->pdo->lastInsertId();

            // Insertion des tags associés
            foreach ($t->tags as $tag) {
                $idTag = $this->stockerTag($tag);

                $requeteTag = $this->pdo->prepare(
                    "INSERT INTO TouiteTag (idTouite, idTag)
                     VALUES (?, ?)"
                );

                $requeteTag->execute([
                    $idTouite,
                    $idTag
                ]);
            }

            $this->pdo->commit();

            // Mise à jour en mémoire
            $t->definirId($idTouite);

            $this->touites[$idTouite] = $t;

            $this->utilisateurs[$pseudo]->ajouterTouite($t);

        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    /**
     * Stocke une donnée selon son type.
     */
    public function stocker(mixed $data): void
    {
        if ($data instanceof Utilisateur) {
            $this->stockerUtilisateur($data);
        } elseif ($data instanceof Tag) {
            $this->stockerTag($data);
        } elseif ($data instanceof Touite) {
            $this->stockerTouite($data);
        } else {
            throw new InvalidArgumentException("Type de donnée inconnu.");
        }
    }

    /**
     * Permet à un utilisateur de suivre un autre utilisateur.
     */
    public function suivreUtilisateur(
        Utilisateur $abonne,
        Utilisateur $suivi
    ): void {
        $this->charger();

        $requete = $this->pdo->prepare(
            "INSERT IGNORE INTO Abonnes (pseudoSuivis, pseudoAbonne)
             VALUES (?, ?)"
        );

        $requete->execute([
            $suivi->pseudo,
            $abonne->pseudo
        ]);

        $abonne->suivreUtilisateur($suivi);
    }

    /**
     * Permet à un utilisateur de suivre un tag.
     */
    public function suivreTag(Utilisateur $utilisateur, Tag $tag): void
    {
        $this->charger();

        $idTag = $this->stockerTag($tag);

        $requete = $this->pdo->prepare(
            "INSERT IGNORE INTO AbonnesTag (pseudo, idTag)
             VALUES (?, ?)"
        );

        $requete->execute([
            $utilisateur->pseudo,
            $idTag
        ]);

        $utilisateur->suivreTag($tag);
    }
}
