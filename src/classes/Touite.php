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
    private array $tags = []; // // Liste vide avec par défaut 0 tag

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
            throw new LengthTouiteException("Le message doit contenir au maximum 235 caractères"); // correction du nom exact de l'exception (L majuscule et nn minuscule)
        } else {
            $this->message = $message;
        }
        $this->auteur = $auteur;
        $this->date = $date;

        // On extrait les tags valides du message de plus, les invalides sont ignorés
        $this->extraireTags($message);

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

    public function extraireTags(String $message) : void
    {
        //sélectionne tout les tags du message
        preg_match_all('/#([\p{L}\p{N}_]+)/u', $message, $trouver); // on stock les tags dans trouver[1] qui contient la liste des libellés sans le #
        // on met tous les tags trouvé un par 1 dans le libelle
        foreach ($trouver[1] as $libelle) {
            if (Tag::estLibelleValide($libelle)) {
                $this->tags[] = new Tag(0, $libelle, "");
            }
        }
    }
}
