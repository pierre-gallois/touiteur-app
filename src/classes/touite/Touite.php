<?php

namespace iutnc\touite\touite;

use iutnc\touite\exception\lengthTouiteException;

/**
 * Classe représentant un touite
 */
class Touite
{
    // Attributs
    private string $message;
    private array $tags;

    /**
     * Constructeur qui construit un touite
     * @param message le message du touite
     * @param tags associés au touite
     */
    public function __construct(string $message, array $tags)
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
