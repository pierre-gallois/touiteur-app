<?php

namespace iutnc\touiteur\classes;

use iutnc\touiteur\exception\LengthTouiteException;
use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\exception\InvalidPropertyException;

/**
 * Classe représentant un touite
 */
class Touite
{
    // Attributs
    private string $message;      // Le contenu du touite 
    private string $date;          // La date du touite
    private Utilisateur $auteur; // L'auteur du touite
    private Tag $tags; // Le(s) tag(s) du touite

    /**
     * Constructeur qui construit un touite
     * @param message le message du touite
     * @param auteur du touite
     * @param date du touite
     */
    public function __construct(string $message, Utilisateur $auteur, string $date)
    {
        // On vérifie que le message ne dépasse pas les 235 caractères
        if (mb_strlen($message, 'UTF-8') > 235) {
            throw new lengthTouiteException("Le message doit contenir au maximum 235 caractères");
        } else {
            $this->message = $message;
        }
        $this->auteur = $auteur;
        $this->date = $date;
    }

    /**
     * Methode __get (getter magique)
     * @return l'attribut passé en paramètre
     */
    public function __get($name): mixed
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        } else {
            throw new InvalidPropertyException("L'attribut $name n'est pas un attribut de Touite.");
        }
    }
}
