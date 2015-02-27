<?php

require_once UTILPATH . 'SessionRegistry.php';

class BaseViewModel {

    public $registry;
    public $task;

    public function __construct() {
        $this->registry = SessionRegistry::getInstance();
    }

    public function __set($name, $value) {
        $this->vars[$name] = $value;
    }

    public function __get($name) {
        return $this->vars[$name];
    }
}