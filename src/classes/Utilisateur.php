<?php

namespace iutnc\touiteur\classes;

use iutnc\touiteur\exception\InvalidPropertyException;

/**
 * Classe représentant un utilisateur
 */
class Utilisateur {
    // Attributs
    private Profil $profil; // profil de l'utilisateur
    private string $pseudo; // pseudo de l'utilisateur
    private array $touitesPublies = []; // touites publiés par l'utilisateur
    private array $touitesSuivis = []; // touites des utilisateurs suivis
    private array $tags = []; // touites par tag donné
    private array $utilisateursSuivis = []; // Utilisateurs que suit(vent) cet utilisateur
    private array $utilisateursAbonnes = []; // utilisateurs abonnés à cet utilisateur
    private array $tagsSuivis = []; // Tags suivis par l'utilisateur


    /**
     * Constructeur d'un utilisateur
     * @param Profil $profil de l'utilisateur
     * @param string $pseudo de l'utilisateur
     */
    public function __construct(Profil $profil, string $pseudo) {
        $this->profil = $profil;
        $this->pseudo = $pseudo;
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
            throw new InvalidPropertyException("L'attribut $name n'est pas un attribut de Utilisateur.");
        }
    }


    /**
     * Methode ajouterTouite qui ajoute un touite
     * @param Touite $touite qu'on veut ajouter
     * @return void
     */
    public function ajouterTouite(Touite $touite) : void
    {
        $this->touitesPublies[] = $touite;
        $this->profil->incrementNbTouite();
    }


    /**
     * Methode pour suivre un utilisateur.
     * @param Utilisateur $user l'utilisateur à suivre
     * @return void
     */
    public function suivreUtilisateur(Utilisateur $user) : void
    {
        if ($user !== $this) { // On vérifie que l'utilisateur ne veut pas s'auto-suivre
            // Vérifier que l'utilisateur ne le suit pas déjà avant de l'ajouter dans utilisateursSuivis.
            if (!in_array($user, $this->utilisateursSuivis)) {
                $this->utilisateursSuivis[] = $user;
                $user->utilisateursAbonnes[] = $this; // On ajoute à l'utilisateur qu'on suit, qu'on est abonné à lui
            }
        }
    }


    /**
     * Methode pour suivre un tag
     * @param Tag $tag
     * @return void
     */
    public function suivreTag(Tag $tag) : void
    {
        $this->tagsSuivis[] = $tag;
    }

    /**
     * Méthode permettant de connecter un utilisateur.
     * @return void
     */
    public function connecter(): void
    {
        // Démarrer la session si elle n'est pas déjà active
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Enregistrer le pseudo dans la session
        $_SESSION['pseudo'] = $this->pseudo;
    }
}

?>
