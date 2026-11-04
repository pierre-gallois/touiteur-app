<?php

namespace iutnc\touiteur\repository;

use iutnc\touiteur\classes\Touite;
use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\classes\Tag;

/**
 * 
 */
abstract class Repository {

    private static ?self $instance = null;


    // Protégé : redéfini dans les enfants
    private function __construct(){}

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
}