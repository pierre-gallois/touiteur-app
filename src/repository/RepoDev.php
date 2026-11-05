<?php

namespace iutnc\touiteur\repository;

use iutnc\touiteur\classes\Profil;
use iutnc\touiteur\classes\Tag;
use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\classes\Touite;
use InvalidArgumentException;

/**
 * Imite un vrai Repository qui ferait le lien entre le code et la base de donnée
 * On a pas encore la base de donnée, alors celui-ci stocke dans des attributs.
 */
class RepoDev extends Repository {
    // Les "tables" de la "base de donnée"
    private array $touites;
    private array $utilisateurs;
    private array $tags;


    protected function __construct() {
        $this->touites = [];
        $this->utilisateurs = [];
        $this->tags = [];
    }


    private function stockerTouite(Touite $touite) : void
    {
        if (array_key_exists($touite->auteur->pseudo, $this->utilisateurs)) {
            $this->touites[$touite->id] = $touite;
        }
    }


    private function stockerUtilisateur(Utilisateur $utilisateur) : void
    {
        $this->utilisateurs[$utilisateur->pseudo] = $utilisateur;
    }


    private function stockerTag(Tag $tag) : void
    {
        $libelle = $tag->libelleTag;

        // Le tag n'est pas encore enregistré
        if (!array_key_exists($libelle, $this->tags)) {
            // Enregistrer le nouveau tag.
            $this->tags[$libelle] = $tag;
            return;
        }
    }


    public function stocker(mixed $data) : void
    {
        if ($data instanceof Touite){
            $nombreTouites = count($this->touites);
            $this->stockerTouite($data);
            if (count($this->touites) > $nombreTouites) {
                foreach ($data->tags as $tag) {
                    $this->stockerTag($tag);
                }
            }
        } elseif ($data instanceof Utilisateur) {
            $this->stockerUtilisateur($data);
        } elseif ($data instanceof Tag) {
            $this->stockerTag($data);
        }
        else {
            throw new \InvalidArgumentException("Le type de donnée entrée ne peut pas être stocké.");
        }
    }


    public function getTouite(mixed $id) : Touite
    {
        if (array_key_exists($id, $this->touites)) return $this->touites[$id];
        throw new InvalidArgumentException("Aucun touite avec cet id");
    }


    public function getUtilisateur(mixed $id) : Utilisateur
    {
        if (array_key_exists($id, $this->utilisateurs)) return $this->utilisateurs[$id];
        throw new InvalidArgumentException("Aucun utilisateur avec cet id");
    }


    public function getTag(mixed $id) : Tag
    {
        if (array_key_exists($id, $this->tags)) return $this->tags[$id];
        throw new InvalidArgumentException("Aucun tag avec cet id");
    }


    public function getTouites() : array
    {
        return $this->touites;
    }


    public function getUtilisateurs() : array
    {
        return $this->utilisateurs;
    }


    public function getTags() : array
    {
        return $this->tags;
    }

    /**
     * Methode qui charge un fichier JSON.
     * Attention : elle ajoute aux données existante les données du JSON
     */
    public function chargerDonnees(string $fichierJSON) : void
    {
        // Le fichier n'existe pas
        if (!file_exists($fichierJSON))
            throw new \RuntimeException("Le fichier \"".$fichierJSON."\" est introuvable.");

        // Avoir le json en
        $json = json_decode(file_get_contents($fichierJSON), true);

        // Enregistrer les utilisateurs
        $usersData  = $json["users"];
        foreach ($usersData as $pseudo => $userData) {
            // Recréer l'utilisateur
            $profil = new Profil($userData["nom"], $userData["prenom"], $userData["email"]);
            $user = new Utilisateur($profil, $pseudo);
            // Le stocker
            $this->stocker($user);
        }

        // Enregistrer les tag
        $tagsData = $json["tags"];
        foreach ($tagsData as $tagData) {
            // Recréer le tag
            $tag = new Tag($tagData["libelle"], $tagData["description"]);
            // Le stocker
            $this->stocker($tag);
        }

        // Enregistrer les relations entre les utilisateurs et les tags.
        foreach ($usersData as $pseudo => $userData) {
            // Récupérer l'utilisateur qui suit les relations.
            $user = $this->utilisateurs[$pseudo];

            // Enregistrer les utilisateurs suivis si la liste existe.
            if (isset($userData["utilisateursSuivis"])) {
                foreach ($userData["utilisateursSuivis"] as $pseudoSuivi) {
                    $user->suivreUtilisateur($this->utilisateurs[$pseudoSuivi]);
                }
            }

            // Enregistrer les tags suivis si la liste existe.
            if (isset($userData["tagsSuivis"])) {
                foreach ($userData["tagsSuivis"] as $libelleTag) {
                    $user->suivreTag($this->tags[$libelleTag]);
                }
            }
        }

        // Enregistrer les touites
        $touitesData = $json["touites"];
        foreach ($touitesData as $touiteData) {
            // Recréer le touite
            $touite = new Touite($touiteData["message"], $this->utilisateurs[$touiteData["auteur"]], $touiteData["date"]);
            // Le stocker
            $this->stocker($touite);
            // Ajouter le touite à la liste des publications de son auteur.
            $touiteDataAuteur = $this->utilisateurs[$touiteData["auteur"]];
            $touiteDataAuteur->ajouterTouite($touite);
        }
    }
}
