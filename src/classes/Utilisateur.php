<?php

namespace iutnc\touiteur\classes;

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
}
