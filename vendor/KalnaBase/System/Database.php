<?php

/**
 *
 * @Singleton to create database connection
 *
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 * @version //autogentag//
 * @license new bsd http://www.opensource.org/licenses/bsd-license.php
 * @filesource
 * @package Database
 * @Author Kevin Waterson
 *
 */

namespace KalnaBase\System;

//use KalnaBase\Classes;
//defined('DIRSEP') or define('DIRSEP', DIRECTORY_SEPARATOR);
//defined('SITEPATH') or define('SITEPATH', realpath(dirname(__FILE__) . DIRSEP . '..' . DIRSEP . '..' . DIRSEP . '..') . DIRSEP);
//defined('VENDORPATH') or define('VENDORPATH', realpath(SITEPATH) . DIRSEP . 'vendor' . DIRSEP);
//defined('COREPATH') or define('COREPATH', VENDORPATH . 'KalnaBase' . DIRSEP);
//require_once COREPATH.'/Classes/EPDOStatement.php';

class Database {

    /**
     * Holds an insance of self
     * @var $instance
     */
    private static $instance = NULL;
    private static $driver = NULL;
    public static $FETCH_OBJ;
    public static $FETCH_ASSOC;
    private static $sql = '';
    public static $boundParameters = [];
    public static $unusedParameters = [];
    public static $sqlWithValues = '';

    /**
     *
     * the constructor is set to private so
     * so nobody can create a new instance using new
     *
     */
    private function __construct() {
        
    }

    /**
     *
     * Return DB instance or create intitial connection
     *
     * @return object (PDO)
     *
     * @access public
     *
     */
    public static function getInstance() {

        if (!self::$instance) {
            $appConfig = AppConfig::getInstance();
            $db_type = $appConfig->values['database']['db_type'];
            $hostname = $appConfig->values['database']['db_hostname'];
            $dbname = $appConfig->values['database']['db_name'];
            $db_password = $appConfig->values['database']['db_password'];
            $db_username = $appConfig->values['database']['db_username'];
            $db_port = $appConfig->values['database']['db_port'];
            switch ($db_type) {
                case 'oracle':
                case 'oci':
                    self::$driver = 'OCI';
                    break;

                default:
                    self::$driver = 'PDO';
                    break;
            }
            switch (self::$driver) {
                case 'PDO':
                    self::$instance = new \PDO("$db_type:host=$hostname;port=$db_port;dbname=$dbname", $db_username, $db_password);
                    self::$instance->setAttribute(\PDO::ATTR_STATEMENT_CLASS, array("KalnaBase\System\EPDOStatement", array(self::$instance)));
                    self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    self::$FETCH_ASSOC = \PDO::FETCH_ASSOC;
                    self::$FETCH_OBJ = \PDO::FETCH_OBJ;
                    break;
                case 'OCI':
                    self::$instance = new \PDO("$db_type:dbname=//$hostname:$db_port/$dbname", $db_username, $db_password);
                    self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    self::$FETCH_ASSOC = \PDO::FETCH_ASSOC;
                    self::$FETCH_OBJ = \PDO::FETCH_OBJ;
//                    self::$instance = oci_pconnect($db_username, $db_password, $hostname);
//                    self::$FETCH_ASSOC = OCI_ASSOC;
                    break;

                default:
                    break;
            }
        }
        return self::$instance;
    }

    public static function getConnection() {
        return self::getInstance();
    }

//    public function bindParam($parameter, &$variable, $data_type = 'PDO::PARAM_STR', $length = null, $driver_options = null) {
//        $this->boundParameters[$parameter] = $variable;
//        return self::$instance->bindParam($parameter, $variable, $data_type, $length, $driver_options);
//    }
//
//    public function prepare($statement, array $driver_options = NULL) {
//        die(__METHOD__);
//        $this->sql = $statement;
//        return self::$instance->prepare($statement, $driver_options);
//    }
//
//    public function execute(array $input_parameters = NULL) {
//        foreach ($this->boundParameters as $parameterKey => $parameterValue) {
//            if (strpos($this->sql, $parameterKey) !== FALSE) {
//                $this->sql = str_replace($parameterKey, $parameterValue, $this->sql, 1);
//            } else {
//                $this->unusedParameters[$parameterKey] = $parameterValue;
//            }
//        }
//        $this->boundParameters = [];
//        return self::$instance->execute($input_parameters);
//    }

    /**
     *
     * Like the constructor, we make __clone private
     * so nobody can clone the instance
     *
     */
    private function __clone() {
        
    }

}

// end of class
