<?php
namespace iutnc\touiteur\action;

class DefaultAction extends Action{
    /**
     * Constructeur de DefaultAction
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Méthode execute qui va afficher une page par défaut
     * @return string la page au format html
     */
    public function execute(): string
    {
        return "<h1> Bienvenue sur Touiteur</h1>";
    }
}