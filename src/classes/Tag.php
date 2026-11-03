<?php

namespace iutnc\touiteur\classes;

use iutnc\touiteur\exception\InvalidTagException;
use iutnc\touiteur\exception\InvalidPropertyException;

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

        $this->idTag = $idTag;
        $this->libelleTag = $libelleTag;
        $this->descriptionTag = $descriptionTag;
    }


    /**
     * Vérifie que le libellé contient uniquement des lettres, chiffres ou _
     * @param libelle le libellé à vérifier
     * @return bool true si le libellé est valide, false sinon
     */
    public static function estLibelleValide(string $libelle): bool
    {
        return preg_match('/^[\p{L}\p{N}_]+$/u', $libelle) === 1;
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
            throw new InvalidPropertyException("L'attribut $name n'est pas un attribut de Tag");
        }
    }
}
