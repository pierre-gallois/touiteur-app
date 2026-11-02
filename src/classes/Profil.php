<?php

namespace iutnc\touiteur\classes;

/**
 * Classe profil représentant le profil d'un utilisateur de Touiteur
 */
class Profil
{
    // Attributs
    private string $nom; // nom du profil
    private string $prenom; // prenom du profil
    private string $email; // email du profil
    private int $nbTouites = 0; // nombre de touites du profil

    /**
     * Constructeur qui construit un profil
     * @param nom du profil
     * @param prenom du profil
     * @param email du profil
     */
    public function __construct(string $nom, string $prenom, string $email)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
    }
}
