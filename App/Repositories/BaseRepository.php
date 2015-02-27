<?php

namespace App\Repositories;
use KalnaBase\System;
use KalnaBase\Utilities;

//include_once UTILPATH . 'ModelLoader.php';
//include_once SYSPATH . 'Database.php';
require_once (UTIL_FUNC_PATH . 'getAllFunctions.php');
require_once UTILPATH . 'SessionRegistry.php';

class BaseRepository {

    protected $PDOformat;
    protected $format;
    public $connection;
    public $registry;

    public function __construct($format = 'object') {
        $this->registry = Utilities\SessionRegistry::getInstance();
        $this->format = $format;
        switch ($this->format) {
            case 'object':
                $this->PDOformat = \PDO::FETCH_OBJ;
                break;
            case 'array':
                $this->PDOformat = \PDO::FETCH_ASSOC;
                break;
            case 'json':
                $this->PDOformat = \PDO::FETCH_ASSOC;
                break;
            default:
                $this->PDOformat = \PDO::FETCH_OBJ;
                break;
        }
        $this->connection = System\DatabaseAbstraction::getConnection();
    }

}
