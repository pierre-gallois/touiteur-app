<?php

namespace iutnc\touiteur\dispatcher;

use iutnc\touiteur\action\AfficherListeTouiteAction;
use iutnc\touiteur\action\DefaultAction;

/**
 * Class Dispatcher
 */
class Dispatcher
{
    private string $action;

    /**
     * Constructeur de Dispatcher
     */
    public function __construct()
    {
        $this->action = $_GET['action'] ?? 'default';
    }

    /**
     * Méthode run charger d'exécuter les différentes actions
     * @return void
     */
    public function run(): void
    {
        // Stocker le résulat html de l'action demandée
        $resultat = "";

        // Appel de l'action demandée
        switch ($this->action) {
            case "default":
                $obj = new DefaultAction();
                $resultat = $obj->execute();
                break;
            case "afficherListeTouite":
                $obj = new AfficherListeTouiteAction();
                $resultat = $obj->execute();
                break;
        }

        // Affichage du rendu sur le navigateur
        $this->renderPage($resultat);
    }

    /**
     * Methode renderPage qui fait le rendu final pour afficher dans le navigateur
     * @param string $html code html de l'action à afficher
     * @return void
     */
    private function renderPage(string $html): void
    {
        // Document complet html
        echo <<<HTML
        <!DOCTYPE html> 
        <html lang='fr'>
        <head>
        <meta charset='UTF-8'>
        <title>Touiteur</title>
        </head>
        <body>
        $html
        </body>
        </html>
HTML;
    }
}