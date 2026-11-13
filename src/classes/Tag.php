<?php

namespace iutnc\touiteur\classes;

use iutnc\touiteur\exception\InvalidTagException;
use iutnc\touiteur\exception\InvalidPropertyException;

/**
 * Classe représentant un tag
 */
class Tag {
    // Attributs
    private string $libelleTag; // libellé du tag, sans le caractère #
    private string $descriptionTag; // description du tag
    private ?int $idTag = null; // Id du tag

    /**
     * Constructeur qui construit un tag
     * @param string $libelleTag libellé du tag
     * @param string $descriptionTag description du tag
     * @throws InvalidTagException si le libellé est invalide
     */
    public function __construct(string $libelleTag, string $descriptionTag) {

        // On retire les espaces et le # éventuel au début
        $libelleTag = ltrim(trim($libelleTag), '#');

        // On vérifie que le libellé est valide
        if (!self::estLibelleValide($libelleTag)) {
            throw new InvalidTagException("Libellé de tag invalide : " . $libelleTag);
        }

        $this->libelleTag = $libelleTag;
        $this->descriptionTag = $descriptionTag;
    }


    /**
     * Vérifie que le libellé contient uniquement des lettres, chiffres ou _
     * @param string $libelle le libellé à vérifier
     * @return bool true si le libellé est valide, false sinon
     */
    public static function estLibelleValide(string $libelle) : bool
    {
        // Vérifie le début avec (^) jusqu'à la fin ($) : qu'il y a au moins un (ou plus) caractère parmi : lettre (\p{L}), chiffre (\p{N}) ou _ en UTF-8 (u)
        // preg_match retourne 1 si le libellé respecte ce format, 0 sinon
        return preg_match('/^[\p{L}\p{N}_]+$/u', $libelle) === 1;
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
            throw new InvalidPropertyException("L'attribut $name n'est pas un attribut de Tag");
        }
    }

    /**
     * Donne au tag l'id attribué par la base de données
     * @param int $id
     * @return void
     */
    public function definirId(int $id) : void
    {
        $this->idTag = $id;
    }
}
