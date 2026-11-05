<?php

namespace iutnc\touiteur\repository;

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
        $this->tags[$tag->libelleTag] = $tag;
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
}
