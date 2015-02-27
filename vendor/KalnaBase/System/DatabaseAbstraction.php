<?php

/**
 *
 * @Lite weight Database abstraction layer
 *
 * @copyright Copyright (C) 2009 PHPRO.ORG. All rights reserved.
 *
 * @license new bsd http://www.opensource.org/licenses/bsd-license.php
 * @filesource
 * @package Database
 * @Author Kevin Waterson
 *
 */

namespace KalnaBase\System;

use KalnaBase\Utilities\Functions as Func;

//require_once SYSPATH . 'Database.php';

class DatabaseAbstraction extends Database {

    public $pdoFormat = '';
    /*
     * @the errors array
     */
    public $errors = array();

    /*
     * @The app configuration
     */
    private $appConfig = array();
    /*
     * @The sql query
     */
    private $sql = '';

    /**
     * @The name=>value pairs
     */
    private $values = array();

    /**
     * @The select columns
     */
    private $select = array();

    /**
     * @The from tables
     */
    private $from = array();

    /**
     * @The join tables
     */
    private $join = array();

    /**
     * @The where name=>value pairs
     */
    private $where = array();

    /**
     * @The where field=>order pairs
     */
    private $order = array();

    /**
     * @The limit offset=>limit pairs
     */
    private $limit = array();

    /**
     * @The results
     */
    private $results;

    public function __construct($pdoFormat) {
        // Load the AppConfig.ini file
        $this->appConfig = AppConfig::getInstance();
        $this->pdoFormat = $pdoFormat;
        if ($this->pdoFormat == 'object') {
            $this->pdoFormat = \PDO::FETCH_OBJ;
        } else {
            $this->pdoFormat = \PDO::FETCH_ASSOC;
        }
        return $this;
    }

    /**
     *
     * @add a value to the values array
     *
     * @access public
     *
     * @param string $key the array key
     *
     * @param string $value The value
     *
     */
    public function addValue($key, $value) {
        $this->values[$key] = $value;
        return $this;
    }

    /**
     *
     * @set the values
     *
     * @access public
     *
     * @param array
     *
     */
    public function setValues($array) {
        $this->values = $array;
        return $this;
    }

    /**
     *
     * @delete a recored from a table
     *
     * @access public
     *
     * @param string $table The table name
     *
     * @param int ID
     *
     */
    public function delete($table, $id) {
        try {
            // get the primary key name
            $pk = $this->getPrimaryKey($table);
            $sql = "DELETE FROM $table WHERE $pk=:$pk";
            $db = Database::getInstance();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":$pk", $id);
            $stmt->execute();
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    /**
     *
     * @insert a record into a table
     *
     * @access public
     *
     * @param string $table The table name
     *
     * @param array $values An array of fieldnames and values
     *
     * @return int The last insert ID
     *
     */
    public function insert($table, $values = null) {
        $values = is_null($values) ? $this->values : $values;
        $sql = "INSERT INTO $table SET ";

        $obj = new \CachingIterator(new \ArrayIterator($values));

        try {
            $db = db::getInstance();
            foreach ($obj as $field => $val) {
                $sql .= "$field = :$field";
                $sql .= $obj->hasNext() ? ',' : '';
                $sql .= "\n";
            }
            $stmt = $db->prepare($sql);

            // bind the params
            foreach ($values as $k => $v) {
                $stmt->bindParam(':' . $k, $v);
            }
            $stmt->execute($values);
            // return the last insert id
            return ['affectedRows' => $stmt->rowCount(), 'lastId' => $db->lastInsertId()];
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    /**
     * @update a table
     *
     * @access public
     * 
     * @param string $table The table name
     *
     * @param int $id
     *
     * @param array $values
     *
     */
    public function update($table, $id, $values = null) {
        $values = is_null($values) ? $this->values : $values;
        try {
            // get the primary key/
            $pk = $this->getPrimaryKey($table);

            // set the primary key in the values array
            $values[$pk] = $id;

            $obj = new \CachingIterator(new \ArrayIterator($values));

            $db = Database::getInstance();
            $sql = "UPDATE $table SET \n";
            foreach ($obj as $field => $val) {
                $sql .= "$field = :$field";
                $sql .= $obj->hasNext() ? ',' : '';
                $sql .= "\n";
            }
            $sql .= " WHERE $pk=$id";
            $stmt = $db->prepare($sql);

            // bind the params
            foreach ($values as $k => $v) {
                $stmt->bindParam(':' . $k, $v);
            }
            // bind the primary key and the id
            $stmt->bindParam($pk, $id);
            $stmt->execute($values);

            // return the affected rows
            return $stmt->rowCount();
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    /**
     * Get the name of the field that is the primary key
     *
     * @access private
     *
     * @param string $table The name of the table
     *
     * @return string
     *
     */
    private function getPrimaryKey($table) {
        try {
            // get the db name from the config.ini file
            $config = configuration::getInstance();
            $db_name = $config->values['database']['db_name'];

            $db = db::getInstance();
            $sql = "SELECT
                            k.column_name
                            FROM
                            information_schema.table_constraints t
                            JOIN
                            information_schema.key_column_usage k
                            USING(constraint_name,table_schema,table_name)
                            WHERE
                            t.constraint_type='PRIMARY KEY'
                            AND
                            t.table_schema='{$db_name}'
                            AND
                            t.table_name=:table";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':table', $table, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn(0);
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    /**
     * Get the name of the tables that has foreignkeys to $table
     *
     * @access private
     *
     * @param string $table The name of the base table
     *
     * @return array
     *
     */
    public function getDependedTables() {
        try {
            $from = new \ArrayIterator($this->from);
            // get the databasename from the AppConfig.ini file
            $db_name = $this->appConfig->values['database']['db_name'];
            $inValue = '';
            for ($index = 0; $index < count($from); $index++) {
                $inValue .= "'" . $db_name . '/' . $from[$index]['table'] . "'";
                $inValue .= $index + 1 < count($from) ? ',' : '';
            }
            if (strlen($inValue) > 0) {
                $db = Database::getInstance();
                $sql = "SELECT replace(isf.REF_NAME,'" . $db_name . "/','') refTable, replace(isf.FOR_NAME,'" . $db_name . "/','') depTable, isfc.FOR_COL_NAME depColumn, isfc.REF_COL_NAME refColumn
                    FROM information_schema.INNODB_SYS_FOREIGN isf, 
                         information_schema.INNODB_SYS_FOREIGN_COLS isfc
                    WHERE isf.REF_NAME IN (" . $inValue . ")
                    AND isf.ID = isfc.ID";
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $this->from = [];
                return $stmt->fetchAll($this->pdoFormat);
            } else {
                $this->errors[] = 'No tables defined by the method from()';
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    /**
     * Get the name of the tables that has foreignkeys to $table
     *
     * @access private
     *
     * @param string $table The name of the base table
     *
     * @return array
     *
     */
    public function getReferentialTables() {
        try {
            $from = new \ArrayIterator($this->from);
            // get the databasename from the AppConfig.ini file
            $db_name = $this->appConfig->values['database']['db_name'];
            $inValue = '';
            for ($index = 0; $index < count($from); $index++) {
                $inValue .= "'" . $db_name . '/' . $from[$index]['table'] . "'";
                $inValue .= $index + 1 < count($from) ? ',' : '';
            }
            if (strlen($inValue) > 0) {
                $db = Database::getInstance();
                $sql = "SELECT replace(isf.REF_NAME,'" . $db_name . "/','') refTable, replace(isf.FOR_NAME,'" . $db_name . "/','') depTable, isfc.FOR_COL_NAME depColumn, isfc.REF_COL_NAME refColumn
                    FROM information_schema.INNODB_SYS_FOREIGN isf, 
                         information_schema.INNODB_SYS_FOREIGN_COLS isfc
                    WHERE isf.FOR_NAME IN (" . $inValue . ")
                    AND isf.ID = isfc.ID";
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $this->from = [];
                return $stmt->fetchAll($this->pdoFormat);
            } else {
                $this->errors[] = 'No tables defined by the method from()';
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    /**
     * Get the metadata of the tables defined by from()
     *
     * @access public
     *
     * @return array
     *
     */
    public function getAllTableNames() {
        try {
            // get the databasename from the AppConfig.ini file
            $db_name = $this->appConfig->values['database']['db_name'];
            $db = Database::getInstance();
            $sql = "SELECT *
                    FROM information_schema.TABLES
                    WHERE TABLE_SCHEMA = '" . $db_name . "'";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll($this->pdoFormat);
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    /**
     * Get the metadata of the tables defined by from()
     *
     * @access public
     *
     * @return array
     *
     */
    public function getMetadata() {
        try {
            $from = new \ArrayIterator($this->from);
            $inValue = '';
            for ($index = 0; $index < count($from); $index++) {
                $inValue .= "'" . $from[$index]['table'] . "'";
                $inValue .= $index + 1 < count($from) ? ',' : '';
            }
            if (strlen($inValue) > 0) {
                $db = Database::getInstance();
                $sql = "SELECT column_name, 
                           case IS_NULLABLE when 'YES' then 'true' when 'NO' then 'false' end is_nullable, 
                           data_type, 
                           ifnull(CHARACTER_MAXIMUM_LENGTH, NUMERIC_PRECISION) max_length,
                           case COLUMN_KEY
                                when 'PRI' THEN 1
                                else 0
                           end primary_key
                    FROM    information_schema.COLUMNS
                    where   TABLE_NAME IN (" . $inValue . ")
                    ORDER BY ORDINAL_POSITION;";
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $this->from = [];
                return $stmt->fetchAll($this->pdoFormat);
            } else {
                $this->errors[] = 'No tables defined by the method from()';
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    /**
     * @access public
     *
     * @param string $tableAlias
     * 
     * @return DatabaseAbstration DatabaseAbstration object
     *
     */
    public function selectAll($tableAlias) {
        $this->select[] = new selectClause($tableAlias, '*');
        return $this;
    }

    /**
     * @access public
     *
     * @param string $tableAlias Table alias as defined in the from function
     * @param string $field The fieldname
     * @param string $alias An alias for the field
     * 
     * @return DatabaseAbstration DatabaseAbstration object
     *
     */
    public function select($tableAlias, $field, $alias = NULL) {
        $this->select[] = new selectClause($tableAlias, $field, $alias);
        //      $this->select[] = array('tableAlias' => $tableAlias, 'field' => $field, 'alias' => $alias); //new selectClause($tableAlias, $field, $alias);

        return $this;
    }

    /**
     * @access public
     *
     * @param string $table The tablename
     * @param string $alias An alias for the table
     *
     * @return DatabaseAbstration DatabaseAbstration object
     */
    public function from($table, $alias = NULL) {
        $this->from[] = array('table' => $table, 'alias' => $alias);
        return $this;
    }

    /**
     * 
     * @param string $direction
     * @param string $table
     * @param string $alias
     * @param string $equation
     * 
     * @return \KalnaBase\System\DatabaseAbstraction
     */
    public function join($direction, $table, $alias, $equation) {
        $this->join[] = array('direction' => $direction, 'table' => $table, 'alias' => $alias, 'equation' => $equation);
        return $this;
    }

    /**
     * 
     * @param string $table
     * @param string $alias
     * @param string $equation
     * 
     * @return \KalnaBase\System\DatabaseAbstraction
     */
    public function joinLeft($table, $alias, $equation) {
        return $this->join('left', $table, $alias, $equation);
    }

    /**
     * 
     * @param string $table
     * @param string $alias
     * @param string $equation
     * 
     * @return \KalnaBase\System\DatabaseAbstraction
     */
    public function joinRigth($table, $alias, $equation) {
        return $this->join('right', $table, $alias, $equation);
    }

    /**
     * 
     * @param string $table
     * @param string $alias
     * @param string $equation
     * 
     * @return \KalnaBase\System\DatabaseAbstraction
     */
    public function joinInner($table, $alias, $equation) {
        return $this->join('inner', $table, $alias, $equation);
    }

//    /**
//     * @access public
//     *
//     * @param string $tableAlias Table alias as defined in the from function
//     * @param string $field The fieldname
//     * @param string $value
//     *
//     */
//    public function where($tableAlias, $field, $value) {
//        $this->where[] = array('tableAlias' => $tableAlias, 'field' => $field, 'value' => $value);
//        return $this;
//    }

    /**
     * @access public
     *
     * @param string $tableAlias Table alias as defined in the from function
     * @param string $value Comparison value
     * @param boolean $bool Negotiation
     *
     * @return DatabaseAbstration DatabaseAbstration object
     *
     */
    public function idEquals($tableAlias, $value, $bool = TRUE) {
        $this->where[] = new whereClause($tableAlias, 'id', $bool ? '=' : '!=', $value);
        return $this;
    }

    /**
     * @access public
     *
     * @param string $tableAlias Table alias as defined in the from function
     * @param string $field The fieldname
     * @param string $value Comparison value
     * @param boolean $bool Negotiation
     *
     * @return DatabaseAbstration DatabaseAbstration object
     *
     */
    public function equals($tableAlias, $field, $value, $bool = TRUE) {
        $this->where[] = new whereClause($tableAlias, $field, $bool ? '=' : '!=', $value);
        return $this;
    }

    /**
     * @access public
     *
     * @param string $tableAlias Table alias as defined in the from function
     * @param string $field The fieldname
     * @param string $value Comparison value
     *
     * @return DatabaseAbstration DatabaseAbstration object
     *
     */
    public function greaterThan($tableAlias, $field, $value) {
        $this->where[] = new whereClause($tableAlias, $field, '>', $value);
        return $this;
    }

    /**
     * @access public
     *
     * @param string $tableAlias Table alias as defined in the from function
     * @param string $field The fieldname
     * @param string $value Comparison value
     *
     * @return DatabaseAbstration DatabaseAbstration object
     *
     */
    public function lessThan($tableAlias, $field, $value) {
        $this->where[] = new whereClause($tableAlias, $field, '<', $value);
        return $this;
    }

    /**
     * @access public
     *
     * @param string $tableAlias Table alias as defined in the from function
     * @param string $field The fieldname
     * @param array $values Comparison value
     * @param boolean $bool Negotiation
     *
     * @return DatabaseAbstration DatabaseAbstration object
     *
     */
    public function oneOf($tableAlias, $field, $values, $bool = TRUE) {
        if (count($values) > 0) {
            $int_val = Func\arrayStringify($values);
            echo $int_val . "\n";
            $this->where[] = new whereClause($tableAlias, $field, $bool ? 'IN' : 'NOT IN', $int_val);
        }
        return $this;
    }

    /**
     * @access public
     *
     * @param string $tableAlias Table alias as defined in the from function
     * @param string $field The fieldname
     * @param string $value Comparison value
     * @param boolean $bool Negotiation
     *
     * @return DatabaseAbstration DatabaseAbstration object
     *
     */
    public function contains($tableAlias, $field, $value, $bool = TRUE) {
        $this->where[] = new whereClause($tableAlias, $field, $bool ? 'LIKE' : 'NOT LIKE', '%' . $value . '%');
        return $this;
    }

    /**
     * @access public
     *
     * @param string $tableAlias Table alias as defined in the from function
     * @param string $field The fieldname
     * @param string $value Comparison value
     * @param boolean $bool Negotiation
     *
     */
    public function startsWith($tableAlias, $field, $value, $bool = TRUE) {
        $this->where[] = new whereClause($tableAlias, $field, $bool ? 'LIKE' : 'NOT LIKE', $value . '%');
        return $this;
    }

    /**
     * @access public
     *
     * @param string $tableAlias Table alias as defined in the from function
     * @param string $field The fieldname
     * @param string $value Comparison value
     * @param boolean $bool Negotiation
     *
     * @return DatabaseAbstration DatabaseAbstration object
     *
     */
    public function endsWith($tableAlias, $field, $value, $bool = TRUE) {
        $this->where[] = new whereClause($tableAlias, $field, $bool ? 'LIKE' : 'NOT LIKE', '%' . $value);
        return $this;
    }

    /**
     *
     * @set limit
     *
     * @access public
     *
     * @param int $offset
     * @param int $limit
     *
     * @return string
     *
     * @return DatabaseAbstration DatabaseAbstration object
     *
     */
    public function limit($offset, $limit) {
        $this->limit = array($offset => $limit);
        return $this;
    }

    /**
     *
     * Add and order by
     *
     * @param string $field The fieldname
     * @param string $sort The sorting order 'ASC' or 'DESC'
     * 
     * @return DatabaseAbstration DatabaseAbstration object
     *
     */
    public function orderBy($field, $sort = 'ASC') {
        $this->order[] = array($field => $sort);
        return $this;
    }

    /**
     *
     * @set the values
     *
     * @access public
     *
     * @param string $sql Complete sql statement
     *
     */
    public function setSQL($sql) {
        $this->sql = $sql;
        return $this;
    }

    /**
     *
     * Fetch all records from table
     *
     * @access public
     *
     * @param $table The table name
     *
     * @return array
     *
     */
    public function query() {

        try {
            $db = Database::getInstance();
            $select = new \ArrayIterator($this->select);
//            $objS = new \CachingIterator(new \ArrayIterator($this->select));
            $sql = 'SELECT ';
            for ($index = 0; $index < count($select); $index++) {
                $sql .= $select[$index]->tableAlias . "." . $select[$index]->field . " " . $select[$index]->alias;
                $sql .= $index + 1 < count($select) ? ',' : '';
                $sql .= "\n";
            }

            $from = new \ArrayIterator($this->from);
            $sql .= 'FROM ';
            for ($index = 0; $index < count($from); $index++) {
                $sql .= $from[$index]['table'] . ' ' . $from[$index]['alias'];
                $sql .= $index + 1 < count($from) ? ',' : '';
                $sql .= "\n";
            }
            $join = new \ArrayIterator($this->join);
            for ($index = 0; $index < count($join); $index++) {
                $sql .= strtoupper($join[$index]['direction']) . ' JOIN ';
                $sql .= $join[$index]['table'] . ' ' . $join[$index]['alias'];
                $sql .= ' ON ' . $join[$index]['equation'];
                $sql .= $index + 1 < count($join) ? ',' : '';
                $sql .= "\n";
            }

            $where = new \ArrayIterator($this->where);
            if ($where->count() > 0) {
                $sql .= 'WHERE ';
                for ($index = 0; $index < count($where); $index++) {
                    if (strpos($where[$index]->operator, 'IN') !== FALSE) {
                        $sql .= $where[$index]->tableAlias . '.' . $where[$index]->field . ' ' . $where[$index]->operator . ' (:' . $where[$index]->tableAlias . $where[$index]->field . $index . ')';
                    } else {
                        $sql .= $where[$index]->tableAlias . '.' . $where[$index]->field . ' ' . $where[$index]->operator . ' :' . $where[$index]->tableAlias . $where[$index]->field . $index;
                    }
                    $sql .= "\n";
                    $sql .= $index + 1 < count($where) ? 'AND ' : '';
                }
            }

            $objO = new \ArrayIterator($this->order);
            if ($objO->count() > 0) {
                $sql .= 'ORDER BY ';
                foreach ($objO as $order => $sort) {
                    $sql .= "$order $sort";
                    $sql .= $objO->hasNext() ? ',' : '';
                    $sql .= "\n";
                }
            }


//            $sql .= is_null($this->limit) ? NULL : key($this->limit) . ' ' . $this->limit[key($this->limit)] . "\n";
            $sql = empty($this->sql) ? $sql : $this->sql;
//            echo $sql ."<br>";
            $stmt = $db->prepare($sql);

//            echo $sql.'<br>';
            // bind the params

            for ($index = 0; $index < count($where); $index++) {
                $stmt->bindParam(':' . $where[$index]->tableAlias . $where[$index]->field . $index, $where[$index]->value);
            }

            $stmt->execute();
            if ($this->pdoFormat == 'object') {
                $this->pdoFormat = \PDO::FETCH_OBJ;
            } else {
                $this->pdoFormat = \PDO::FETCH_ASSOC;
            }
            $this->results = $stmt->fetchAll($this->pdoFormat);
            $stmt->closeCursor();
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            $this->results->errors = $this->errors;
        }
        return $this;
//
//        $res = $db->query($this->sql);
//        return $res;
    }

    function groupHierarchical($parentKey, $childName, $childColumns) {
        $childArray = [];
        foreach ($this->results as $i => $element) {
            foreach ($childColumns as $ci => $childColumn) {
                $childArray[$childColumn] = $element[$childColumn];
                unset($this->results[$i][$childColumn]);
            }
            $this->results[$i][$childName] = $childArray;
        }
//        $this->results = array_unique($this->results);
//        print_r($this->results);
        return $this;
    }

    function groupHierarchical2($parentKey, $childName, $childColumns) {
        $parentArray = [];
        $parent1Array = [];
        $childArray = [];
        $tempArray = [];
        foreach ($this->results as $i => $element) {
            $parentArray = array_column($this->results, $parentKey);
            $parent1Array = array_unique($parentArray);
        }
        foreach ($parentArray as $pi => $parent) {
            $tempArray = Func\ArrayFunctions::arrayTrim($this->results, $parentKey, $parent);

            foreach ($tempArray as $ti => $temp) {
                foreach ($childColumns as $ci => $childColumn) {
                    $childArray[$ci][$childColumn] = $tempArray[$ti][$childColumn];
                    unset($this->results[$pi][$childColumn]);
                }
            }
            $this->results[$pi][$childName] = $childArray;
        }
        $parent2Array = array_diff_key($parentArray, $parent1Array);
        foreach ($parent2Array as $i) {
            unset($this->results[$i]);
        }
//        print_r($this->results);
        return $this;
    }

    function fetch() {
        return $this->results;
    }

}

class whereClause {

    /**
     * Private class for the DatabaseAbstraction class
     * @param string $tableAlias
     * @param string $field
     * @param string $operator
     * @param string $value
     */
    public $tableAlias;
    public $field;
    public $operator;
    public $value;

    function __construct($tableAlias, $field, $operator, $value) {
        $this->tableAlias = $tableAlias;
        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;
    }

}

class selectClause {

    /**
     * Private class for the DatabaseAbstraction class
     * @param string $tableAlias
     * @param string $field
     * @param string $alias
     */
    public $tableAlias;
    public $field;
    public $alias;

    function __construct($tableAlias, $field, $alias = NULL) {
        $this->tableAlias = $tableAlias;
        $this->field = $field;
        $this->alias = $alias;
    }

}

// end of class
