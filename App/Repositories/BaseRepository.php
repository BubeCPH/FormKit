<?php

namespace App\Repositories;
use KalnaBase\System;
use KalnaBase\Utilities;

//include_once UTILPATH . 'ModelLoader.php';
//include_once SYSPATH . 'Database.php';
require_once (UTIL_FUNC_PATH . 'getAllFunctions.php');
require_once UTILPATH . 'SessionRegistry.php';

class BaseRepository {

    protected $pdoformat;
    protected $format;
    public $connection;
    public $dba;
    public $registry;

    public function __construct($format = 'object') {
        $this->registry = Utilities\SessionRegistry::getInstance();
        $this->format = $format;
        switch ($this->format) {
            case 'object':
                $this->pdoformat = \PDO::FETCH_OBJ;
                break;
            case 'array':
                $this->pdoformat = \PDO::FETCH_ASSOC;
                break;
            case 'json':
                $this->pdoformat = \PDO::FETCH_ASSOC;
                break;
            default:
                $this->pdoformat = \PDO::FETCH_OBJ;
                break;
        }
        $this->connection = System\DatabaseAbstraction::getConnection();
        $this->dba = new System\DatabaseAbstraction;
    }

}
