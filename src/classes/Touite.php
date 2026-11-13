<?php

namespace iutnc\touiteur\classes;

use iutnc\touiteur\exception\LengthTouiteException;
use iutnc\touiteur\classes\Utilisateur;
use iutnc\touiteur\exception\InvalidPropertyException;
use iutnc\touiteur\classes\Tag;

/**
 * Classe représentant un touite
 */
class Touite {
    // Attributs
    private string $message;      // Le contenu du touite
    private string $date;          // La date du touite
    private Utilisateur $auteur; // L'auteur du touite
    private array $tags = []; // Liste vide avec par défaut 0 tag
    private int $id;
    private int $note = 0; // La note du touite (+1 pour un like, -1 pour un dislike)
    private array $votants = []; // Les pseudos des utilisateurs qui ont déjà noté le touite
    // Générateur d'id
    private static int $increment = 0;


    /**
     * Constructeur qui construit un touite
     * @param string $message le message du touite
     * @param Utilisateur $auteur du touite
     * @param string $date du touite
     * @throws LengthTouiteException si le message dépasse 235 caractères
     */
    public function __construct(string $message, Utilisateur $auteur, string $date) {

        // On vérifie que le message ne dépasse pas les 235 caractères
        if (mb_strlen($message, 'UTF-8') > 235) {
            throw new LengthTouiteException("Le message doit contenir au maximum 235 caractères");
        } else {
            $this->message = $message;
        }
        $this->auteur = $auteur;
        $this->date = $date;

        // On extrait les tags valides du message de plus, les invalides sont ignorés
        $this->extraireTags();
        $this->id = self::$increment++; // Associe un id & incremente pour le prochain.
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
            throw new InvalidPropertyException("L'attribut $name n'est pas un attribut de Touite.");
        }
    }

    /**
     * Methode noter qui ajoute la note d'un utilisateur au touite
     * @param string $pseudo de l'utilisateur qui note
     * @param int $valeur de la note (+1 si approuvé, -1 si rejeté)
     * @return bool true si la note a été ajoutée, false si l'utilisateur avait déjà noté le touite
     */
    public function noter(string $pseudo, int $valeur) : bool
    {
        $dejaNote = false; // par défaut l'utilisateur n'a pas encore fait de vote sur le touite

        // on parcours tout les utilisateurs qui ont déjà noté le touite en question pour voir si il en fait partie ou nn
        foreach ($this->votants as $votant) {
            if ($votant === $pseudo) {
                $dejaNote = true;
            }
        }

        // l'utilisateur ne peut pas noter le meme touite plus d'une fois
        if ($dejaNote) {
            return false;
        }

        // On ajoute la note et on retient que cet utilisateur a noté (= il ne pourra plus le noter ce touite)
        $this->note += $valeur;
        $this->votants[] = $pseudo;
        return true;
    }

    /**
     * Methode extraiteTags qui extrait les tags du message
     * @return void
     */
    private function extraireTags() : void
    {
        //sélectionne tout les tags du message
        preg_match_all('/#([\p{L}\p{N}_]+)/u', $this->message, $trouver); // on stock les tags dans trouver[1] qui contient la liste des libellés sans le #
        // détaille : \p{L} = n'importe quelle lettre (même avec accent), \p{N} = n'importe quel chiffre, et u = mode UTF-8

        // On supprime les doublons possible
        $libellesUniques = array_unique($trouver[1]);

        // on met tous les tags trouvé un par 1 dans le libelle
        foreach ($libellesUniques as $libelle) {
            if (Tag::estLibelleValide($libelle)) {
                $this->tags[] = new Tag($libelle, "");
            }
        }
    }
}

