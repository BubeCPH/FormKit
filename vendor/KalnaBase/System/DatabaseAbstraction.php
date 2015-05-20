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
use KalnaBase\Config;

//require_once SYSPATH . 'Database.php';

class DatabaseAbstraction extends Database {

    private $debug = FALSE;
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
    private $params = [];

    /**
     * @The name=>value pairs
     */
    private $values = array();

    /**
     * @The primarykey for a given table
     * table=>pk_column 
     */
    private $primarykey = array();

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

    /**
     * @The selected table=>column pairs
     */
    private $selectedColumns = array();

    public function __construct($pdoFormat = 'object') {
        // Load the AppConfig.ini file
        $this->appConfig = AppConfig::getInstance();
        $this->pdoFormat = $pdoFormat;
        if (!empty($pdoFormat) && ($pdoFormat === 'object' || $pdoFormat === \PDO::FETCH_OBJ)) {
            $this->pdoFormat = \PDO::FETCH_OBJ;
        } elseif (!empty($pdoFormat) && ($pdoFormat !== 'object' || $pdoFormat === \PDO::FETCH_ASSOC)) {
            $this->pdoFormat = \PDO::FETCH_ASSOC;
        }
        return $this;
    }

    public function generateModelDefinition() {
        $model = [];
        $db = Database::getInstance();
        $appConfig = AppConfig::getInstance();
        $sql = "SELECT TABLE_NAME 
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = '" . $appConfig->values['database']['db_name'] . "'
                ORDER BY TABLE_NAME;";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $tables = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($tables as $table) {
            foreach ($this->from($table['TABLE_NAME'])->getMetadata('php') as $position => $column) {
                $model[$table['TABLE_NAME']][$column['column_name']]['position'] = $position;
                $model[$table['TABLE_NAME']][$column['column_name']]['isNullable'] = $column['is_nullable'] === 'true' ? TRUE : FALSE;
                $model[$table['TABLE_NAME']][$column['column_name']]['dataType'] = $column['data_type'];
                $model[$table['TABLE_NAME']][$column['column_name']]['maxLength'] = (integer) $column['max_length'];
                $model[$table['TABLE_NAME']][$column['column_name']]['numericPrecision'] = (integer) $column['numeric_precision'];
                $model[$table['TABLE_NAME']][$column['column_name']]['numericScale'] = (integer) $column['numeric_scale'];
                $model[$table['TABLE_NAME']][$column['column_name']]['primaryKey'] = $column['primary_key'] === 'true' ? TRUE : FALSE;
                $model[$table['TABLE_NAME']][$column['column_name']]['autoIncrement'] = $column['auto_increment'] === 'true' ? TRUE : FALSE;
            }
        }

        return $model;
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
     * @param int ID
     * @param string $table The table name
     *
     */
    public function delete($id, $table = NULL) {
        $tableInt = is_null($table) && count($this->from) === 1 ? $this->from[0]['table'] : $table;
        try {
            // get the primary key name
            $this->primarykey[$tableInt] = empty($this->primarykey[$tableInt]) ? $this->getPrimaryKey($tableInt) : $this->primarykey[$tableInt];
            $sql = "DELETE FROM $tableInt WHERE " . $this->primarykey[$tableInt] . " = :" . $this->primarykey[$tableInt];
            $db = Database::getInstance();
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':' . $this->primarykey[$tableInt], $id);
            $stmt->execute();
            return ['affectedRows' => $stmt->rowCount()];
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
     * @param array $values An array of fieldnames and values
     * @param string $table The table name
     *
     * @return int The last insert ID
     *
     */
    public function insert($values = NULL, $table = NULL) {
        $tableInt = is_null($table) && count($this->from) === 1 ? $this->from[0]['table'] : $table;
        $valuesInt = is_null($values) ? $this->values : $values;
        $sql = "INSERT INTO `$tableInt` SET ";

        $obj = new \CachingIterator(new \ArrayIterator($valuesInt));

        try {
            $db = Database::getInstance();
            foreach ($obj as $field => $val) {
                $sql .= "`$field` = :$field";
                $sql .= $obj->hasNext() ? ',' : '';
                $sql .= "\n";
            }
            $stmt = $db->prepare($sql);

            if ($this->debug) {
                echo __LINE__ . ' ' . __METHOD__ . ':<br>' . $sql . '<br>';
            }
            // bind the params
            foreach ($valuesInt as $k => $v) {
                $stmt->bindParam(':' . $k, $v);
            }
            $stmt->execute($valuesInt);
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
     * @param int $pkKey
     * @param array $values
     * @param string $table The table name
     * 
     */
    public function update($pkKey = NULL, $values = NULL, $table = NULL) {
        $tableInt = is_null($table) && count($this->from) === 1 ? $this->from[0]['table'] : $table;
        $valuesInt = is_null($values) ? $this->values : $values;

        try {
            if (!is_null($pkKey)) {
                // get the primary key
                $this->pkEquals($pkKey);

                // set the primary key in the values array
//                $valuesInt[$this->primarykey[$tableInt]] = $pkKey;
            }
            if (isset($valuesInt[$this->primarykey[$tableInt]])) {
                unset($valuesInt[$this->primarykey[$tableInt]]);
            }

            $newValues = new \CachingIterator(new \ArrayIterator($valuesInt));
            $db = Database::getInstance();
            $this->sql = "UPDATE `$tableInt` SET \n";
            foreach ($newValues as $field => $value) {
                $this->sql .= "`$field` = :$field";
                $this->sql .= $newValues->hasNext() ? ',' : '';
                $this->sql .= "\n";
            }

            $where = new \ArrayIterator($this->where);
            $whereCount = count($this->where);
            if ($whereCount > 0) {
                $this->sql .= 'WHERE ';
                for ($index = 0; $index < $whereCount; $index++) {
                    if (strpos($where[$index]->operator, 'IN') !== FALSE) {
                        $this->sql .= "`" . $where[$index]->tableAlias . '`.`' . $where[$index]->field . '` ' . $where[$index]->operator . ' (:' . $where[$index]->tableAlias . $where[$index]->field . $index . ')';
                    } else {
                        $this->sql .= "`" . $where[$index]->tableAlias . '`.`' . $where[$index]->field . '` ' . $where[$index]->operator . ' :' . $where[$index]->tableAlias . $where[$index]->field . $index;
                    }
                    $this->sql .= "\n";
                    $this->sql .= $index + 1 < $whereCount ? 'AND ' : '';
                }
            }
            $stmt = $db->prepare($this->sql);
//            if ($this->debug) {
//                echo __LINE__ . ' ' . __METHOD__ . ':<br>' . $this->sql . '<br>';
//            }
            // bind the params
            $this->params = [];
            foreach ($newValues as $field => $value) {
                $stmt->bindValue(':' . $field, $value);
                $this->params[':' . $field] = $value;
            }
            for ($index = 0; $index < $whereCount; $index++) {
                $stmt->bindValue(':' . $where[$index]->tableAlias . $where[$index]->field . $index, $where[$index]->value);
                $this->params[':' . $where[$index]->tableAlias . $where[$index]->field . $index] = $where[$index]->value;
            }
//            var_dump($stmt->queryString);
//            var_dump($stmt->boundParams);
//            var_dump($stmt->usedParams);
//            var_dump($stmt->unusedParams);
//            var_dump($stmt->interpolateQuery());
//            var_dump($stmt->numberOfKeys);
//            var_dump($stmt->numberOfParams);
            $stmt->execute();

            $this->where = [];

            // return the affected rows
            return $stmt->rowCount();
        } catch (Exception $e) {
            $this->where = [];
            $this->errors[] = $e->getMessage();
        }
    }

    /**
     * @insertOrUpdate a table
     *
     * @access public
     * 
     * @param mixed $pkKey nullable
     * @param array $values nullable
     * @param string $table nullable The table name
     */
    public function insertOrUpdate($pkKey = NULL, $values = NULL, $table = NULL) {
        $tableInt = is_null($table) && count($this->from) === 1 ? $this->from[0]['table'] : $table;
        $this->values = is_null($values) ? $this->values : $values;
        $pdoFormat = $this->pdoFormat;
        $this->pdoFormat = \PDO::FETCH_ASSOC;
        $update = FALSE;
        if (is_null($pkKey)) {
            $newId = $this->table($tableInt)->insert();
        } else {
            $existingValues = $this->from($tableInt)->selectAll()->pkEquals($pkKey)->query(\PDO::FETCH_ASSOC)->fetch();
            if (empty($existingValues)) {
                $this->primarykey[$tableInt] = empty($this->primarykey[$tableInt]) ? $this->getPrimaryKey($tableInt) : $this->primarykey[$tableInt];

                // add the primary key to the values array
                $newId = $this->table($tableInt)->addValue($this->primarykey[$tableInt], $pkKey)->insert();
            } else {
                // set the primary key in the values array
                foreach ($this->values as $key => $value) {
                    if ($existingValues[0]->$key != $value) {
                        $update = TRUE;
                    }
                }
                if ($update) {
                    $rowsUpdated = $this->table($tableInt)->pkEquals($pkKey)->update();
                } else {
                    $rowsUpdated = 0;
                }
            }
        }
        $this->pdoFormat = $pdoFormat;
//        var_dump('$rowsUpdated');
        return ($newId) ? $newId : $rowsUpdated;
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
            $db_name = $this->appConfig->values['database']['db_name'];

            $db = Database::getInstance();
            $sql = "SELECT k.column_name
                    FROM information_schema.table_constraints t
                    JOIN information_schema.key_column_usage k 
                    USING (constraint_name , table_schema , table_name)
                    WHERE t.constraint_type = 'PRIMARY KEY'
                      AND t.table_schema = '$db_name'
                      AND t.table_name = :table";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':table', $table, \PDO::PARAM_STR);
            $stmt->execute();
            $this->primarykey[$table] = $stmt->fetchColumn(0);
            return $this->primarykey[$table];
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
     * @param string $dataTypeStyle
     * @param array $columns
     * @return array
     *
     */
    public function getMetadata($dataTypeStyle = 'mysql', $columns = []) {
        $metadata = [];
        $columnsCount = count($columns);
        try {
            if ($columnsCount === 0) {
                $fromCount = count($this->from);
                $inValue = '';
                for ($index = 0; $index < $fromCount; $index++) {
                    $inValue .= "'" . $this->from[$index]['table'] . "'";
                    $inValue .= $index + 1 < $fromCount ? ',' : '';
                }
                if (strlen($inValue) > 0) {
                    $db = Database::getInstance();
                    $appConfig = AppConfig::getInstance();
                    $sql = "SELECT column_name, 
                                   case IS_NULLABLE when 'YES' then 'true' when 'NO' then 'false' end is_nullable, 
                                   data_type, 
                                   case when CHARACTER_MAXIMUM_LENGTH IS NOT NULL
                                        then CHARACTER_MAXIMUM_LENGTH
                                        when NUMERIC_PRECISION IS NOT NULL AND (NUMERIC_SCALE IS NULL OR NUMERIC_SCALE = 0)
                                        then NUMERIC_PRECISION
                                        when NUMERIC_PRECISION IS NOT NULL AND NUMERIC_SCALE IS NOT NULL 
                                        then NUMERIC_PRECISION + 1
                                        else 0
                                   end max_length,
                                   ifnull(NUMERIC_PRECISION, 0) numeric_precision,
                                   ifnull(NUMERIC_SCALE, 0) numeric_scale,
                                   case COLUMN_KEY
                                        when 'PRI' THEN 'true'
                                        else 'false'
                                   end primary_key,
                                   case EXTRA
                                        when 'auto_increment' THEN 'true'
                                        else 'false'
                                   end auto_increment
                            FROM    information_schema.COLUMNS
                            WHERE   TABLE_NAME IN (" . $inValue . ")
                            AND TABLE_SCHEMA = '" . $appConfig->values['database']['db_name'] . "'
                            ORDER BY ORDINAL_POSITION;";
                    $stmt = $db->prepare($sql);
                    $stmt->execute();
                    $this->from = [];
                    $metadata = $stmt->fetchAll($this->pdoFormat);
                }
            } elseif ($columnsCount > 0) {
                $db = Database::getInstance();
                $columns = new \CachingIterator(new \ArrayIterator($columns));
                $sql = "";
                foreach ($columns as $column) {
                    $alias = empty($column['alias']) ? $column['field'] === '*' ? 'column_name' : $column['field'] : $column['alias'];
                    $sql .= "SELECT column_name, 
                                    " . $alias . " alias_name, 
                                    case IS_NULLABLE when 'YES' then 'true' when 'NO' then 'false' end is_nullable, 
                                    data_type, 
                                    ifnull(CHARACTER_MAXIMUM_LENGTH, NUMERIC_PRECISION) max_length,
                                    case COLUMN_KEY
                                         when 'PRI' THEN 'true'
                                         else 'false'
                                    end primary_key
                            FROM    information_schema.COLUMNS
                            WHERE   TABLE_NAME = '" . $column['table'] . "' ";
                    $sql .= $column['field'] === '*' ? '' : "AND   column_name = " . $column['field'] . " " . "\n";
                    $sql .= $columns->hasNext() ? "UNION " . "\n" : ";";
                }
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $metadata = $stmt->fetchAll($this->pdoFormat);
            } else {
                $this->errors[] = 'No tables defined by the method from() or provided as parameters';
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
        if (!empty($metadata)) {
            if ($dataTypeStyle === 'php') {
                $metadataCount = sizeof($metadata);
                for ($i = 0; $i < $metadataCount; $i++) {
                    if (!empty($metadata[$i]['data_type'])) {
                        $metadata[$i]['data_type'] = Config\Utilities::mysqlToPhpDataType($metadata[$i]['data_type']);
                    }
                }
            }
//            elseif ($dataTypeStyle === 'ms') {
//                $metadataCount = sizeof($metadata);
//                for ($i = 0; $i < $metadataCount; $i++) {
//                    foreach (Config\Utilities::mysqlToMsTypeConversions as $phpDataType => $mysqlDataType) {
//                        if (in_array($metadata[$i]['data_type'], $mysqlDataType)) {
//                            $metadata[$i]['data_type'] = $phpDataType;
//                        }
//                    }
//                }
//            }
            return $metadata;
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
    public function selectAll($tableAlias = NULL) {
        $tableAliasInt = is_null($tableAlias) && count($this->from) === 1 ? $this->from[0]['alias'] : $tableAlias;
        $this->select[] = new selectClause($tableAliasInt, '*');
        return $this;
    }

    /**
     * @access public
     *
     * @param string $field The fieldname
     * @param string $alias An alias for the field
     * @param string $tableAlias Table alias as defined in the from function
     * 
     * @return DatabaseAbstration DatabaseAbstration object
     *
     */
    public function select($field, $alias = NULL, $tableAlias = NULL) {
        $tableAliasInt = is_null($tableAlias) && count($this->from) === 1 ? $this->from[0]['alias'] : $tableAlias;
        $this->select[] = new selectClause($tableAliasInt, $field, $alias);
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
    public function from($table) {
        $fromKey = NULL;
        foreach ($this->from as $key => $value) {
            if ($value['table'] === $table && $value['alias'] === $table) {
                $fromKey = $key;
            }
        }
        if (is_null($fromKey)) {
            $this->from[] = array('table' => $table, 'alias' => $table);
        }
        return $this;
    }

    /**
     * @access public
     *
     * @param string $table The tablename
     * @param string $alias An alias for the table
     * 
     * @see from
     *
     * @return DatabaseAbstration DatabaseAbstration object
     */
    public function into($table, $alias = NULL) {
        return $this->from($table, $alias);
    }

    /**
     * @access public
     *
     * @param string $table The tablename
     * @param string $alias An alias for the table
     * 
     * @see from
     *
     * @return DatabaseAbstration DatabaseAbstration object
     */
    public function table($table, $alias = NULL) {
        return $this->from($table, $alias);
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
     * @param string $value Comparison value
     * @param string $tableAlias Table alias as defined in the from function
     * @param boolean $bool Negotiation
     *
     * @return DatabaseAbstration DatabaseAbstration object
     *
     */
    public function pkEquals($value, $tableAlias = NULL, $bool = TRUE) {
        $tableAliasInt = is_null($tableAlias) && count($this->from) === 1 ? $this->from[0]['alias'] : $tableAlias;
        $tableInt = '';
        foreach ($this->from as $from) {
            if ($from['alias'] === $tableAliasInt) {
                $tableInt = $from['table'];
            }
        }
        $this->where[] = new whereClause($tableAliasInt, $this->getPrimaryKey($tableInt), $bool ? '=' : '!=', $value);
        return $this;
    }

    /**
     * @access public
     *
     * @param string $field The fieldname
     * @param string $value Comparison value
     * @param string $tableAlias Table alias as defined in the from function
     * @param boolean $bool Negotiation
     *
     * @return DatabaseAbstration DatabaseAbstration object
     *
     */
    public function equals($field, $value, $tableAlias = NULL, $bool = TRUE) {
        $tableAliasInt = is_null($tableAlias) && count($this->from) === 1 ? $this->from[0]['alias'] : $tableAlias;
        $this->where[] = new whereClause($tableAliasInt, $field, $bool ? '=' : '!=', $value);
        return $this;
    }

    /**
     * @access public
     *
     * @param string $field The fieldname
     * @param string $value Comparison value
     * @param string $tableAlias Table alias as defined in the from function
     *
     * @return DatabaseAbstration DatabaseAbstration object
     *
     */
    public function greaterThan($field, $value, $tableAlias = NULL) {
        $tableAliasInt = is_null($tableAlias) && count($this->from) === 1 ? $this->from[0]['alias'] : $tableAlias;
        $this->where[] = new whereClause($tableAliasInt, $field, '>', $value);
        return $this;
    }

    /**
     * @access public
     *
     * @param string $field The fieldname
     * @param string $value Comparison value
     * @param string $tableAlias Table alias as defined in the from function
     *
     * @return DatabaseAbstration DatabaseAbstration object
     *
     */
    public function lessThan($field, $value, $tableAlias = NULL) {
        $tableAliasInt = is_null($tableAlias) && count($this->from) === 1 ? $this->from[0]['alias'] : $tableAlias;
        $this->where[] = new whereClause($tableAliasInt, $field, '<', $value);
        return $this;
    }

    /**
     * @access public
     *
     * @param string $field The fieldname
     * @param array $values Comparison value
     * @param string $tableAlias Table alias as defined in the from function
     * @param boolean $bool Negotiation
     *
     * @return DatabaseAbstration DatabaseAbstration object
     *
     */
    public function oneOf($field, $values, $tableAlias = NULL, $bool = TRUE) {
        $tableAliasInt = is_null($tableAlias) && count($this->from) === 1 ? $this->from[0]['alias'] : $tableAlias;

        if (count($values) > 1) {
            $int_val = Func\ArrayFunctions::arrayStringify($values);
//            echo $int_val . "\n";
        } elseif (is_array($values) && count($values) === 1) {
            $int_val = Func\ArrayFunctions::arrayStringify($values);
//            echo $int_val . "\n";
        } elseif (!is_array($values) && count($values) === 1) {
            $int_val = $values;
//            echo $int_val . "\n";
        }
        $this->where[] = new whereClause($tableAliasInt, $field, $bool ? 'IN' : 'NOT IN', $int_val);
        return $this;
    }

    /**
     * @access public
     *
     * @param string $field The fieldname
     * @param string $value Comparison value
     * @param string $tableAlias Table alias as defined in the from function
     * @param boolean $bool Negotiation
     *
     * @return DatabaseAbstration DatabaseAbstration object
     *
     */
    public function contains($field, $value, $tableAlias = NULL, $bool = TRUE) {
        $tableAliasInt = is_null($tableAlias) && count($this->from) === 1 ? $this->from[0]['alias'] : $tableAlias;
        $this->where[] = new whereClause($tableAliasInt, $field, $bool ? 'LIKE' : 'NOT LIKE', '%' . $value . '%');
        return $this;
    }

    /**
     * @access public
     *
     * @param string $field The fieldname
     * @param string $value Comparison value
     * @param string $tableAlias Table alias as defined in the from function
     * @param boolean $bool Negotiation
     *
     */
    public function startsWith($field, $value, $tableAlias = NULL, $bool = TRUE) {
        $tableAliasInt = is_null($tableAlias) && count($this->from) === 1 ? $this->from[0]['alias'] : $tableAlias;
        $this->where[] = new whereClause($tableAliasInt, $field, $bool ? 'LIKE' : 'NOT LIKE', $value . '%');
        return $this;
    }

    /**
     * @access public
     *
     * @param string $field The fieldname
     * @param string $value Comparison value
     * @param string $tableAlias Table alias as defined in the from function
     * @param boolean $bool Negotiation
     *
     * @return DatabaseAbstration DatabaseAbstration object
     *
     */
    public function endsWith($field, $value, $tableAlias = NULL, $bool = TRUE) {
        $tableAliasInt = is_null($tableAlias) && count($this->from) === 1 ? $this->from[0]['alias'] : $tableAlias;
        $this->where[] = new whereClause($tableAliasInt, $field, $bool ? 'LIKE' : 'NOT LIKE', '%' . $value);
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
     * @get eventually errors
     *
     * @access public
     *
     */
    function getErrors() {
        return $this->errors;
    }

    /**
     *
     * @get the SQL-statement
     *
     * @access public
     *
     */
    public function getSQL() {
        return $this->sql;
    }

    /**
     *
     * @get the parameters for the SQL-statement
     *
     * @access public
     *
     */
    public function getParams() {
        return $this->params;
    }

    /**
     *
     * @set the SQL-statement
     *
     * @access public
     *
     * @param string $sql Complete sql statement
     *
     */
    public function setSQL($sql) {
        $this->sql = $sql;
        if ($this->debug) {
            echo __LINE__ . ' ' . __METHOD__ . ':<br>' . $sql . '<br>';
        }
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
    public function query($pdoFormat = NULL) {
        if (!empty($pdoFormat) && ($pdoFormat === 'object' || $pdoFormat = \PDO::FETCH_OBJ)) {
            $this->pdoFormat = \PDO::FETCH_OBJ;
        } elseif (!empty($pdoFormat) && ($pdoFormat !== 'object' || $pdoFormat = \PDO::FETCH_ASSOC)) {
            $this->pdoFormat = \PDO::FETCH_ASSOC;
        }
        try {
            $db = Database::getInstance();
            if (empty($this->sql)) {
                $select = new \ArrayIterator($this->select);
                $from = new \ArrayIterator($this->from);
                $join = new \ArrayIterator($this->join);
                $where = new \ArrayIterator($this->where);
                $ordreBy = new \ArrayIterator($this->order);
                $selectCount = count($this->select);
                $fromCount = count($this->from);
                $joinCount = count($this->join);
                $whereCount = count($this->where);
                $ordreByCount = count($this->order);

//            $objS = new \CachingIterator(new \ArrayIterator($this->select));
                $this->sql = 'SELECT ';
                for ($index = 0; $index < $selectCount; $index++) {
                    $this->sql .= "`" . $select[$index]->tableAlias . "`." . ($select[$index]->field === '*' ? $select[$index]->field : "`" . $select[$index]->field . "` ") . $select[$index]->alias;
                    $this->sql .= $index + 1 < count($select) ? ',' : '';
                    $this->sql .= "\n";

                    for ($innerIndex = 0; $innerIndex < $fromCount; $innerIndex++) {
                        if ($from[$innerIndex]['alias'] === $select[$index]->tableAlias) {
                            $this->selectedColumns[] = ['table' => $from[$innerIndex]['table'], 'field' => $select[$index]->field, 'alias' => $select[$index]->alias];
                        }
                    }
                }

                $this->sql .= 'FROM ';
                for ($index = 0; $index < $fromCount; $index++) {
                    $this->sql .= "`" . $from[$index]['table'] . "` `" . $from[$index]['alias'] . "` ";
                    $this->sql .= $index + 1 < count($from) ? ',' : '';
                    $this->sql .= "\n";
                }

                for ($index = 0; $index < $joinCount; $index++) {
                    $this->sql .= strtoupper($join[$index]['direction']) . ' JOIN ';
                    $this->sql .= "`" . $join[$index]['table'] . "` " . $join[$index]['alias'];
                    $this->sql .= ' ON ' . $join[$index]['equation'];
                    $this->sql .= $index + 1 < count($join) ? ',' : '';
                    $this->sql .= "\n";
                }

                if ($whereCount > 0) {
                    $this->sql .= 'WHERE ';
                    for ($index = 0; $index < $whereCount; $index++) {
                        if (strpos($where[$index]->operator, 'IN') !== FALSE) {
                            $this->sql .= "`" . $where[$index]->tableAlias . '`.`' . $where[$index]->field . '` ' . $where[$index]->operator . ' (:' . $where[$index]->tableAlias . $where[$index]->field . $index . ')';
                        } else {
                            $this->sql .= "`" . $where[$index]->tableAlias . '`.`' . $where[$index]->field . '` ' . $where[$index]->operator . ' :' . $where[$index]->tableAlias . $where[$index]->field . $index;
                        }
                        $this->sql .= "\n";
                        $this->sql .= $index + 1 < $whereCount ? 'AND ' : '';
                    }
                }

                if ($ordreByCount > 0) {
                    $ordreBy = new \CachingIterator($ordreBy);
                    $this->sql .= 'ORDER BY ';
                    foreach ($ordreBy as $order => $sort) {
                        $this->sql .= "`$order` $sort";
                        $this->sql .= $ordreBy->hasNext() ? ',' : '';
                        $this->sql .= "\n";
                    }
                }
//            $this->sql .= is_null($this->limit) ? NULL : key($this->limit) . ' ' . $this->limit[key($this->limit)] . "\n";
            }

            $stmt = $db->prepare($this->sql);
            if ($this->debug) {
                echo __LINE__ . ' ' . __METHOD__ . ':<br>' . $this->sql . '<br>';
            }
            // bind the params
            for ($index = 0; $index < $whereCount; $index++) {
                $stmt->bindValue(':' . $where[$index]->tableAlias . $where[$index]->field . $index, $where[$index]->value);
                $this->params[':' . $where[$index]->tableAlias . $where[$index]->field . $index] = $where[$index]->value;
            }

            $stmt->execute();
//            if ($this->pdoFormat == 'object') {
//                $this->pdoFormat = \PDO::FETCH_OBJ;
//            } else {
//                $this->pdoFormat = \PDO::FETCH_ASSOC;
//            }
            $this->results = $stmt->fetchAll($this->pdoFormat);
            $stmt->closeCursor();
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
            $this->results->errors = $this->errors;
        }
        $this->select = [];
        $this->from = [];
        $this->join = [];
        $this->where = [];
        return $this;
//
//        $res = $db->query($this->sql);
//        return $res;
    }

    function castValues() {
        if (count($this->results) > 0 && count($this->selectedColumns) > 0) {
            $metadata = $this->getMetadata('php', $this->selectedColumns);
            foreach ($this->results as $resultKey => $results) {
                foreach ($results as $valueKey => $value) {
                    foreach ($metadata as $meta) {
                        if ($valueKey == $meta['alias_name'] && $meta['data_type'] === 'integer') {
                            $this->results[$resultKey][$valueKey] = (integer) $value;
                        } elseif ($valueKey == $meta['alias_name'] && $meta['data_type'] === 'string') {
                            $this->results[$resultKey][$valueKey] = (string) $value;
                        } elseif ($valueKey == $meta['alias_name'] && $meta['data_type'] === 'datetime') {
                            $this->results[$resultKey][$valueKey] = new \DateTime($value);
                        } elseif ($valueKey == $meta['alias_name'] && $meta['data_type'] === 'boolean' &&
                                (strtolower($value) === 'true' || $value === '1')) {
                            $this->results[$resultKey][$valueKey] = (boolean) TRUE;
                        } elseif ($valueKey == $meta['alias_name'] && $meta['data_type'] === 'boolean' &&
                                (strtolower($value) === 'false' || $value === '0')) {
                            $this->results[$resultKey][$valueKey] = (boolean) FALSE;
                        }
                    }
                }
            }
        }
        return $this;
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

    function clear() {
        $this->select = [];
        $this->from = [];
        $this->join = [];
        $this->where = [];
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
