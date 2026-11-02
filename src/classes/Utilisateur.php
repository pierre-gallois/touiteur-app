<?php

namespace iutnc\touiteur\classes;

/** classe pour l'utilisateur */
class Utilisateur
{
    private string $pseudo;
    private array $touites;

    /**
     * Constructeur qui construit un utilisateur
     */
    public function __construct($pseudo, array $touites = [])
    {
        $this->pseudo = $pseudo;
        $this->touites = $touites;

    }

    public function getPseudo(): string
    {
        return $this->pseudo;
    }

}