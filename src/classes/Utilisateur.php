<?php

namespace iutnc\touiteur\classes;

/** classe pour l'utilisateur */
class Utilisateur
{
    private string $pseudo; // le nom d'utilisateur
    private array $touites; //  les poste de l'utilisateur

    /**
     * Constructeur qui construit un utilisateur
     * @param pseudo du touites
     * @param touites de l'utilisateur
     */
    public function __construct($pseudo, array $touites = [])
    {
        $this->pseudo = $pseudo;
        $this->touites = $touites;

    }

    public function getPseudo(): string
    {
        // on retourne le pseudo de l'utilisateur
        return $this->pseudo;
    }

}