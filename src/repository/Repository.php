<?php

namespace iutnc\touiteur\repository;

use iutnc\touiteur\classes\Touite;
use iutnc\touiteur\classes\Utilisateur;

/**
 * 
 */
abstract class Repository {

    private static ?self $instance = null;


    // Protégé : redéfini dans les enfants
    protected abstract function __construct();

    /**
     * Méthode singleton : retourne une seule et unique instance.
     * Il n'est pas possible d'avoir
     * @return static
     */
    public static function getInstance(): static
    {
        if (self::$instance === null) {
            // Appel du constructeur de la classe enfant
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * Méthode interne pour stocker les données
     * @return int l'id de l'
     */
    protected abstract function stocker(string $table, mixed $data) : int;

    public function enregistrer(mixed $data) : int
    {
        $id = -1;
        if ($data instanceof Touite) {
            $id = $this->stocker("touites", $data);
        }
        elseif ($data instanceof Utilisateur) {
            $id = $this->stocker("utilisateurs", $data);
        }
        else {
            throw new \InvalidArgumentException("Le type de donnée entrée ne peut pas être stocké.");
        }
        return $id;
    }
    public abstract function recuperer(string $table, int $id) : mixed;
}