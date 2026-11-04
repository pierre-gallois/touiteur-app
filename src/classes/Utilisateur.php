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
    private array $utilisateursSuivis = []; // Utilisateurs que suit(vent) cet utilisateur
    private array $touitesAbonnes = []; // utilisateurs abonnés à cet utilisateur
    private array $tagsSuivis=[]; // Tags suivis par l'utilisateur

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
     * @param name de l'attribut
     * @return l'attribut passé en paramètre
     * @throw InvalidPropertyException est levée si l'attribut n'existe pas
     */
    public function __get(string $name): mixed
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        } else {
            throw new InvalidPropertyException("L'attribut $name n'est pas un attribut de Utilisateur.");
        }
    }

    /**
     * Methode ajouterTouite qui ajoute un touite
     * @param Touite $touite qu'on veut ajouter
     * @return void
     */
    public function ajouterTouite(Touite $touite): void
    {
        $this->touitesPublies[] = $touite;
    }

<<<<<<< HEAD
=======
    /**
     * Methode pour suivre un utilisateur.
     * @param Utilisateur $user l'utilisateur à suivre
     * @return void
     */
    public function suivreUtilisateur(Utilisateur $user) : void {
        $this->utilisateursSuivis[] = $user;
    }

    /**
    *Methode pour suivre un tag
    *@param Tag $tag
    *@return void
    */
    public function suivreTag(Tag $tag){
        $this->tagsSuivis[]=$tag;
    }
>>>>>>> 8e49bc9f523ca131baf9d817585bf5cd469d4930
}

?>

