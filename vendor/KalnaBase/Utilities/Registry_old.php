 <?php

/**
* Registers objects and variables
*
* Makes objects and variables available to any level
* of the application without having to keep track
* of their existence.  Also useful for objects such
* as database connectors that are used globaly and
* not to be duplicated.
*
* PHP version 5
*
* @package PLAR
* @category Data
* @author Chris Pliakas <cpliakas@gmail.com>
*/
namespace KalnaBase\Utilities;
class Registry
{
    ///////////////////////////////////////
    //       Private Properties
    ///////////////////////////////////////

    /**
     * Registry of variables and objects
     * @access private
     * @var array
     */
    static private $registry = array();

    ///////////////////////////////////////
    //          Public Methods
    ///////////////////////////////////////

    /**
     * Adds an item to the registry
     * @access public
     * @param string item's unique name
     * @param mixed item
     * @return boolean
     */
    static public function add($name, &$item)
    {
        if (!self::exists($name)) {
            self::$registry[$name] = $item;
            return true;
        } else {
            return false;
        }
    }
    /**
     * Adds an value to the registry
     * @access public
     * @param string value's unique name
     * @param mixed item
     * @return boolean
     */
    static public function addValue($name, $item)
    {
        if (!self::exists($name)) {
            self::$registry[$name] = $item;
            return true;
        } else {
            return false;
        }
    }
    /**
     * Updates an item in the registry
     * @access public
     * @param string item's unique name
     * @param mixed item
     * @return boolean
     */
    static public function update($name, &$item)
    {
        if (self::exists($name)) {
            self::$registry[$name] = $item;
            return true;
        } else {
            return false;
        }
    }
    /**
     * Updates an value in the registry
     * @access public
     * @param string value's unique name
     * @param mixed item
     * @return boolean
     */
    static public function updateValue($name, $item)
    {
        if (self::exists($name)) {
            self::$registry[$name] = $item;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns true if item is registered
     * @access public
     * @param string item's name
     * @return boolean
     */
    static public function exists($name)
    {
        if (is_string($name)) {
            return array_key_exists($name, self::$registry);
        } else {
            throw new Exception('Registry item\'s name must be a string');
        }
    }

    /**
     * Returns registered item
     * @access public
     * @param string item's name
     * @return mixed (null if name is not in registry)
     */
    static public function &get($name)
    {
        if (self::exists($name)) {
            $return = self::$registry[$name];
        } else {
            $return = null;
        }
        return $return;
    }

    /**
     * Returns registered item
     * @access public
     * @param string item's name
     * @return mixed (null if name is not in registry)
     */
    static public function getAll()
    {
        return self::$registry;
    }

    /**
     * Removes a registry entry
     * @access public
     * @param string item's name
     * @return boolean
     */
    static public function remove($name)
    {
        if (self::exists($name)) {
            unset(self::$registry[$name]);
        }
        return true;
    }

    /**
     * Clears the entire registry
     * @access public
     * @return boolean
     */
    static public function clear()
    {
        self::$registry = array();
    }
}


///////////////////////////////////////////////////////
//     Example of how `registry` can be
//     used to retrieve variables within
//     any scope of the application.
///////////////////////////////////////////////////////
//
//    //calls registry class
//    require_once 'Registry.php';
//
//    //sets and registers a variable
//    $item = 'Here is a registered variable';
//    Registry::add('Variable', $item);
//
//    /**
//    * Test class that echos a registered variable
//    */
//    class test
//    {
//        private $item;
//
//        public function __construct()
//        {
//            $this->item = Registry::get('Variable');
//        }
//
//        public function get()
//        {
//            echo '<p>'.$this->item.'</p>';
//        }
//    }
//
//    //will return "Here is a registered variable"
//    $test = new test();
//    $test->get();
//
//    //tests if "Variable" exists
//    if (Registry::exists('Variable')) {
//        echo '<p>"Variable" exists</p>';
//    } else {
//        echo '<p>"Variable" does not exists</p>';
//    }
//
//    //tests if "variable" exists
//    if (Registry::exists('variable')) {
//        echo '<p>"variable" exists</p>';
//    } else {
//        echo '<p>"variable" does not exists</p>';
//    }
//
//    //removes "Variable"
//    Registry::remove('Variable');