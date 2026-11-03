<?php

namespace iutnc\touiteur\repository;

use iutnc\touiteur\repository\Repository;
use iutnc\touiteur\classes\Touite;

/**
 * Imite un vrai Repository qui ferait le lien entre le code et la base de donnée
 * On a pas encore la base de donnée, alors celui-ci stocke dans des attributs.
 */
class RepoDev extends Repository {
    public static string $TOUITES      = "touites";
    public static string $UTILISATEURS = "utilsateurs";

    // Les "tables" de la "base de donnée"
    private array $touites;
    private array $utilisateurs;
    private int $increment;

    protected function __construct(){
        $this->touites = [];
        $this->utilisateurs = [];
        $this->increment = 0;
    }


    protected function stocker(string $table, mixed $data): int
    {
        $this->$table[$this->increment++] = $data;
        return $this->increment - 1;
    }

    public function recuperer(string $table, int $id) : mixed {
        return $this->$table[$id];
    }
}
