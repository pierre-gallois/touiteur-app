<?php

namespace iutnc\touiteur\classes;

use iutnc\touiteur\exception\InvalidPropertyException;

/**
 * Classe profil représentant le profil d'un utilisateur de Touiteur
 */
class Profil {
    // Attributs
    private string $nom; // nom du profil
    private string $prenom; // prenom du profil
    private string $email; // email du profil
    private int $nbTouites = 0; // nombre de touites du profil


    /**
     * Constructeur qui construit un profil
     * @param string $nom du profil
     * @param string $prenom du profil
     * @param string $email du profil
     */
    public function __construct(string $nom, string $prenom, string $email) {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
    }


    /**
     * Methode __get (getter magique)
     * @param string $name de l'attribut
     * @return mixed l'attribut passé en paramètre
     * @throws InvalidPropertyException est levée si l'attribut n'existe pas
     */
    public function __get(string $name) : mixed
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        } else {
            throw new InvalidPropertyException("L'attribut $name n'est pas un attribut de Profil.");
        }
    }


    /**
     * Methode incrementNbTouite pour augmenter le nombre de touite
     * @return void
     */
    public function incrementNbTouite() : void
    {
        $this->nbTouites++;
    }
}
