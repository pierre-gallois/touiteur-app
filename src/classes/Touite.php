<?php
/**
 * Classe représentant un touite
 */
class Touite{
    // Attributs
    private String $message;
    private array $tags;

    /**
     * Constructeur qui construit un touite
     * @param message le message du touite
     * @param tags associés au touite
     */
    public function __construct($message, $tags)
    {
        $this->message = $message;
        $this->tags = $tags
    }
}