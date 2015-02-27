<?php

namespace KalnaBase\Utilities;

use KalnaBase\Utilities\Functions as Func;

class LanguageLoader {
    /*
     * @var array $language_values; 
     */

    public $values = array();
    public $lang_file;

    /*
     * @var object $instance
     */
    private static $instance = null;

    /**
     *
     * Return Config instance or create intitial instance
     *
     * @access public
     *
     * @return object
     *
     */
    public static function getInstance($language) {
        if (is_null(self::$instance)) {
            self::$instance = new LanguageLoader($language);
        }
        return self::$instance;
    }

    /**
     *
     * @the constructor is set to private so
     * @so nobody can create a new instance using new
     *
     */
    private function __construct($language) {
        switch ($language) {
            case 'en':
                $this->lang_file = 'en_US.php';
                break;

            case 'da':
                $this->lang_file = 'da_DK.php';
                break;

            case 'no':
                $this->lang_file = 'nb_NO.php';
                break;

            default:
                $this->lang_file = 'da_DK.php';
        }
        include LANGPATH . 'en_US.php';
        $this->defaultValues = $language_strings;
        if (file_exists(LANGPATH . $this->lang_file)) {
            include LANGPATH . $this->lang_file;
            $this->values = $language_strings; //parse_ini_file(LANGPATH . $this->lang_file, true);
            $this->values =  Func\ArrayFunctions::arrayFillIn($this->values, $this->defaultValues);
        }

// check if there is a module config file
        $module_conf = LANGPATH . '/ALL.php';
        if (file_exists($module_conf)) {
            $module_array = parse_ini_file($module_conf, true);
            $this->values = array_merge($this->values, $module_array);
        }
    }

    /**
     * @get a config option by key
     *
     * @access public
     *
     * @param string $key:The configuration setting key
     *
     * @return string
     *
     */
    public function getValue($key) {
        return self::$values[$key];
    }

    /**
     *
     * @__clone
     *
     * @access private
     *
     */
    private function __clone() {
        
    }

}

//
//<!--//session_start();
////header('Cache-control: private'); // IE 6 FIX
//
//public static function getLanguage($language = 'da') {
//if(isSet($language))
//{
//$lang = $language;
//
//// register the session and set the cookie
//$_SESSION['lang'] = $lang;
//
//setcookie('lang', $lang, time() + (3600 * 24 * 30));
//}
//else if(isSet($_SESSION['lang']))
//{
//$lang = $_SESSION['lang'];
//}
//else if(isSet($_COOKIE['lang']))
//{
//$lang = $_COOKIE['lang'];
//}
//else
//{
//$lang = 'da';
//}
//
//
//
//return LANGPATH.'/'.$lang_file;
//} -->
