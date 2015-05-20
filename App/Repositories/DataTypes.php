<?php

namespace App\Repositories;

/**
 * Description of DataTypes
 *
 * @author:     Claus Hjort Bube <cb at kalna.dk>
 * @org_author: 
 * @created:    26-04-2015
 * @return:     ?               //string, int, decimal, array, function
 * 
 * @name:       DataTypes
 * @version:    0.1
 * @desc:       class for 
 */
class DataTypes extends BaseRepository {

    public $json;
    public $datatypes;
    public $affectedRows;

    // the constructor!
    public function __construct($format = 'object') {
        parent::__construct($format);
    }

    public function fetchDataTypes() {
        $sql = "SELECT * FROM data_types";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $this->datatypes = $stmt->fetchAll($this->pdoformat);
        $stmt->closeCursor();
    }

    public function getDataTypes() {
        empty($this->datatypes) ? self::fetchDataTypes() : NULL;

        return $this->format == 'json' ? json_encode($this->datatypes) : $this->datatypes;
    }

    public function saveDataTypes($values = []) {
        $this->affectedRows = 0;
        foreach ($values as $value) {
            $this->affectedRows += $this->dba->table('data_types')->insertOrUpdate($value['id'], array('name' => $value['name'], 'datatype' => $value['datatype'], 'description' => $value['description']));
        }
//        for ($i = 0; $i < count($values); $i++) {
//            $this->affectedRows += $this->dba->update('data_types', $values[$i]['id'], array('name' => $values[$i]['name'], 'datatype' => $values[$i]['datatype'], 'description' => $values[$i]['description']));
//        }
    }

}
