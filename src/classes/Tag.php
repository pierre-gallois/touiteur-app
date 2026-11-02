<?php

namespace iutnc\touiteur\classes;

use iutnc\touiteur\exception\InvalidTagException;

/**
 * Classe représentant un tag
 */
class Tag
{
    // Attributs
    private int $idTag; // identifiant du tag
    private string $libelleTag; // libellé du tag, sans le caractère #
    private string $descriptionTag; // description du tag

    /**
     * Constructeur qui construit un tag
     * @param idTag identifiant du tag
     * @param libelleTag libellé du tag
     * @param descriptionTag description du tag
     * @throws InvalidTagException si le libellé est invalide
     */
    public function __construct(int $idTag, string $libelleTag, string $descriptionTag)
    {
        // On retire les espaces et le # éventuel au début
        $libelleTag = ltrim(trim($libelleTag), '#');

        // On vérifie que le libellé est valide
        if (!self::estLibelleValide($libelleTag)) {
            throw new InvalidTagException("Libellé de tag invalide : " . $libelleTag);
        }

        $this -> idTag = $idTag;
        $this -> libelleTag = $libelleTag;
        $this -> descriptionTag = $descriptionTag;
    }


    /**
     * Vérifie que le libellé contient uniquement des lettres, chiffres ou _
     * @param libelle le libellé à vérifier
     * @return bool true si le libellé est valide, false sinon
     */
    private static function estLibelleValide(string $libelle): bool
    {
        return preg_match('/^[\p{L}\p{N}_]+$/u', $libelle) === 1;
    }

    /**
     * Retourne l'identifiant du tag
     */
    public function getIdTag(): int
    {
        return $this -> idTag;
    }

    /**
     * Retourne le libellé du tag
     */
    public function getLibelleTag(): string
    {
        return $this -> libelleTag;
    }

    /**
     * Retourne la description du tag
     */
    public function getDescriptionTag(): string
    {
        return $this -> descriptionTag;
    }
}