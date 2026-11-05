<?php

namespace iutnc\touiteur\action;

class DefaultAction extends Action{
    /**
     * Constructeur de DefaultAction
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Méthode execute qui va afficher une page par défaut
     * @return string la page au format html
     */
    public function execute() : string
    {
        return <<<HTML
<h1>Bienvenue sur Touiteur</h1>
<ul>
<li><a href="index.php?action=publier-touite">Publier un touite</a></li>
<li><a href="index.php?action=touites-utilisateur">Afficher un touite d'un utilisateur</a></li>
<li><a href="index.php?action=touites">Liste des touites</a></li>
<li><a href="index.php?action=show-influenceurs">Voir la liste des influenceurs</a></li>
</ul>
HTML;

    }
}