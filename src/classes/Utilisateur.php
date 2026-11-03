<?php

namespace iutnc\touiteur\classes;
use iutnc\touiteur\exception\InvalidPropertyException;

/**
 * Classe représentant un utilisateur
 */
class Utilisateur
{
    // Attributs
    private Profil $profil; // profil de l'utilisateur
    private string $pseudo; // pseudo de l'utilisateur
    private array $touitesPublies = []; // touites publiés par l'utilisateur
    private array $touitesSuivis = []; // touites des utilisateurs suivis
    private array $tags = []; // touites par tag donné

    /**
     * Constructeur d'un utilisateur
     * @param profil de l'utilisateur
     * @param pseudo de l'utilisateur
     */
    public function __construct(Profil $profil, string $pseudo)
    {
        $this->profil = $profil;
        $this->pseudo = $pseudo;
    }

    /**
     * Methode __get (getter magique)
     * @return l'attribut passé en paramètre
     */
    public function __get($name): mixed
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        } else {
            throw new InvalidPropertyException("L'attribut $name n'est pas un attribut de Utilisateur.");
        }
    }

    public function ajouterTouite(Touite $touite): void {
            $this->touitesPublies[] = $touite;
    }
}

?>

