<?php

namespace iutnc\touiteur\dispatcher;

use iutnc\touiteur\exception\DispatcherException;
use iutnc\touiteur\exception\DispatcherException;

/**
 * Class Dispatcher
 */
class Dispatcher {
    protected ?string $action = null;

    protected array $actions = [];

    public function __construct() {
        $this->action = $_GET['action'] ?? 'default';
    }

    public function addRoute(string $action, string $action_class): self {
        $this->actions[$action] = $action_class;
        return $this;
    }

    public function run(): string {
        if (isset($this->actions[$this->action])) {
            $action_class = $this->actions[$this->action];
            return (new $action_class())->execute();
        }
        else throw new DispatcherException("action ".$this->action." non trouvée.");
    }
}
