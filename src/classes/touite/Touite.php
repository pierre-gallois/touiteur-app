<?php

namespace iutnc\touite;

use iutnc\touite\exception\LengthTouiteException;

/**
 * Classe représentant un touite
 */
class Touite
{
    // Attributs
    private string $message;      // Le contenu du touite 
    private array $tags;          // Les #tags
    private Utilisateur $autheur; // L'auteur du touite
    private string $date;

    /**
     * Constructeur qui construit un touite
     * @param message le message du touite
     * @param tags associés au touite
     * @param date du touite
     */
    public function __construct(string $message, array $tags, $date)
    {
        // On vérifie que le message ne dépasse pas les 235 caractères
        if (mb_strlen($message, 'UTF-8') > 235) {
            throw new lengthTouiteException("Le message doit contenir au maximum 235 caractères");
        } else {
            $this->message = $message;
        }

        $this->tags = $tags;
    }
}
