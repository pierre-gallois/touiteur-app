<?php

namespace iutnc\touite\touite;
/** classe pour l'utilisateur */
class utilisateur
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