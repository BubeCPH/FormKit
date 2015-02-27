<?php

namespace KalnaBase\Utilities;

abstract class AbstractRegistry {

    protected static $_instances = array();

// get Singleton instance of the registry (uses the 'get_called_class()' function available in PHP 5.3)

    public static function getInstance() {

// resolves the called class at run time

        $class = get_called_class();

        if (!isset(self::$_instances[$class])) {

            self::$_instances[$class] = new $class;
        }

        return self::$_instances[$class];
    }

// overridden by some subclasses

    protected function __construct() {
        
    }

// prevent cloning instance of the registry

    protected function __clone() {
        
    }

// implemented by subclasses

    abstract public function set($key, $value);

// implemented by subclasses

    abstract public function get($key);

// implemented by subclasses

    abstract public function clear();
}
