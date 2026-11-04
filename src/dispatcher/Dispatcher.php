<?php

namespace iutnc\touiteur\dispatcher;

use iutnc\touiteur\action\AfficherListeTouiteAction;

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
        $resultat = "";
        switch ($this->action) {
            case "default":
                // TODO faire defaultAction
                break;
            case "afficherListeTouite":
                $obj = new AfficherListeTouiteAction();
                $resultat = $obj->execute();
                break;
        }
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