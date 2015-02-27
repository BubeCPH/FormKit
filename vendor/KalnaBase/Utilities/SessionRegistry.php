<?php

namespace KalnaBase\Utilities;

//require_once UTILPATH . 'AbstractRegistry.php';

class SessionRegistry extends AbstractRegistry {

    private $forbidden = array('set', 'get', 'exists', 'getAll', 'update', 'remove', 'clear');
    private $special = array('date', 'year', 'month', 'week');

// protected constructor

    protected function __construct() {
        session_start();
    }

    /**
     * Returns true if item is registered
     * @access public
     * @param string item's key
     * @return boolean
     */
    public function exists($key) {
        if (is_string($key)) {
            return isset($_SESSION[$key]);
        } else {
            throw new Exception('Registry item\'s name must be a string');
        }
    }

// serve data to the session registry

    public function set($key, $value) {
        if (in_array($key, $this->forbidden)) {
            throw new Exception("Registry item's key can't be in 'set', 'get', 'exists', 'getAll', 'update', 'remove', 'clear'");
        } elseif (in_array($key, $this->special)) {
            $this->setSpecial($key, $value);
        } else {
            $_SESSION[$key] = $value;
        }
    }

    /*
     * *
     * ** The __set magic method will be used to add new objects to the SessionRegistry
     * ** (http://www.php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members)
     * *
     */

    public function __set($key, $value) {
        $this->set($key, $value);
    }

// get session data from the session registry

    public function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : NULL;
    }

    /*
     * *
     * ** The magic method __get will be used when were trying to pull objects from the SessionRegistry
     * *
     */

    public function __get($key) {
        return $this->get($key);
    }

// get session data from the session registry

    public function getAll() {

        return $_SESSION;
    }

    /**
     * Updates an item in the registry
     * @access public
     * @param string item's unique key
     * @param mixed item
     * @return boolean
     */
    public function update($key, &$item) {
        if ($this->exists($key)) {
            $_SESSION[$key] = $item;
            return true;
        } else {
            return false;
        }
    }

// get session data from the session registry
    /**
     * Removes a registry entry
     * @access public
     * @param string item's name
     * @return boolean
     */
    public function remove($key) {

        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
        return true;
    }

// clear the state of the session registry

    public function clear() {
        session_unset(); // Same as $_SESSION = array();
        session_destroy(); // Destroy session on disk
        session_start();
        session_regenerate_id(true);
    }

    public function setSpecial($key, $value) {
//        die('setSpecial: ' . $key . '/' . $value);
        switch ($key) {
            case 'date':
                $date = new \DateTime($value);
                $_SESSION['year'] = (int) $date->format('Y');
                $_SESSION['weekYear'] = (int) $date->format('o');
                $_SESSION['month'] = (int) $date->format('m');
                $_SESSION['week'] = (int) $date->format('W');
                $_SESSION['date'] = $date->format('Y-m-d');
                break;
            case 'year':
                $date = new \DateTime($this->date);
                $date = $value . "-" . date('m', $date) . "-" . date('d', $date);
                $this->setSpecial('date', $date);
                break;
            case 'month':
                $date = new \DateTime($this->date);
                $date = date('Y', $date) . "-" . $value . "-" . date('d', $date);
                $this->setSpecial('date', $date);
                break;
            case 'week':
                $currDate = new \DateTime($this->date);
                $date = new \DateTime();
                $date->setISODate($currDate->format('Y'), $value, $currDate->format('N'));
                $_SESSION['year'] = (int) $date->format('Y');
                $_SESSION['weekYear'] = (int) $date->format('o');
                $_SESSION['month'] = (int) $date->format('m');
                $_SESSION['week'] = (int) $date->format('W');
                $_SESSION['date'] = $date->format('Y-m-d');
                break;

            default:
                break;
        }
    }

}

//    // include source classes
//
//    require_once 'SessionRegistry.php';
//
//    // get Singleton instance of the SessionRegistry class
//
//    $sessionRegistry = SessionRegistry::getInstance();
//
//    // save some data to the session registry
//
//    $sessionRegistry->set('user', 'Susan Norton');
//
//    // get data from the session registry
//
//    echo 'Full name of user : ' . $sessionRegistry->get('user');
