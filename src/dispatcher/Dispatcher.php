<?php

namespace iutnc\touiteur\dispatcher;

use iutnc\touiteur\exception\DispatcherException;

/**
 * Class Dispatcher
 */
class Dispatcher {
    protected ?string $action = null;
    protected array $actions = [];

    /**
     * Constructeur de Dispatcher
     */
    public function __construct() {
        $this->action = $_GET['action'] ?? 'default';
    }

    /**
     * Ajoute une route au dispatcher
     * @param string $action le nom de l'action
     * @param string $action_class le nom de la classe associée
     * @return self
     */
    public function addRoute(string $action, string $action_class): self {
        $this->actions[$action] = $action_class;
        return $this;
    }

    /**
     * Méthode run chargée d'exécuter les différentes actions
     * @return string le code HTML généré par l'action
     * @throws DispatcherException si l'action n'est pas trouvée
     */
    public function run(): string {
        if (isset($this->actions[$this->action])) {
            $action_class = $this->actions[$this->action];
            return (new $action_class())->execute();
        } else {
            throw new DispatcherException("action " . $this->action . " non trouvée.");
        }
    }
}