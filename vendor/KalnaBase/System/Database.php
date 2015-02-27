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

class Database {

    /**
     * Holds an insance of self
     * @var $instance
     */
    private static $instance = NULL;
    private static $driver = NULL;
    public static $FETCH_OBJ;
    public static $FETCH_ASSOC;

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
