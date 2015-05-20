<?php

namespace KalnaBase\Config;

/**
 * Description of Utilities
 *
 * @author:     Claus Hjort Bube <cb at kalna.dk>
 * @org_author: 
 * @created:    14-05-2015
 * @return:     ?               //string, int, decimal, array, function
 * 
 * @name:       Utilities
 * @version:    0.1
 * @desc:       class for 
 * 
 * @param
 * - foo are required
 * - bar are optional
 * 
 * @example
 * $m = new email ( "hello there",                           // foo
 *                  "how are you?"                           // bar
 *                );
 * 
 * $m->method();
 */
class Utilities {

//    public static $mysqlToPhpTypeConversions = ['integer' => ['bigint', 'int', 'mediumint', 'smallint', 'tinyint'], 'string' => ['varchar', 'text'], 'boolean' => ['bit'], 'date' => ['date'], 'datetime' => ['dateTime'], 'time' => ['time']];
    public static $mysqlToMsTypeConversions = ['bigint' => 'Int64', 'int' => 'Int32', 'mediumint' => 'Int32', 'smallint' => 'Int16', 'tinyint' => 'Int16', 'varchar' => 'String', 'text' => 'String', 'bit' => 'Boolean', 'date' => 'Date', 'datetime' => 'DateTime', 'time' => 'Time'];
    public static $mysqlToPhpTypeConversions = ['bigint' => 'integer', 'int' => 'integer', 'mediumint' => 'integer', 'smallint' => 'integer', 'tinyint' => 'integer', 'varchar' => 'string', 'text' => 'string',
        'bit' => 'boolean', 'date' => 'datetime', 'datetime' => 'datetime', 'time' => 'datetime'];

// the constructor!
    public function __construct($foo, $bar) {
        throw new Exception("Class Utilities Not implemented.");
    }

    public static function mysqlToPhpDataType($value) {
//        switch ($value) {
//            case 'bigint':
//                return 'integer';
//
//            case 'int':
//                return 'integer';
//
//            case 'mediumint':
//                return 'integer';
//
//            case 'smallint':
//                return 'integer';
//
//            case 'tinyint':
//                return 'integer';
//
//            case 'varchar':
//                return 'string';
//
//            case 'text':
//                return 'string';
//
//            case 'bit':
//                return 'boolean';
//
//            case 'date':
//                return 'datetime';
//
//            case 'datetime':
//                return 'datetime';
//
//            case 'time':
//                return 'datetime';
//
//            default:
//                return $value;
//}
        return empty(self::$mysqlToPhpTypeConversions[$value]) ? $value : self::$mysqlToPhpTypeConversions[$value];
    }

    public static function castValueMysqlToPhpDataType($dataType, $value) {
        switch ($dataType) {
            case 'bigint':
                return 'integer';

            case 'int':
                return 'integer';

            case 'mediumint':
                return 'integer';

            case 'smallint':
                return 'integer';

            case 'tinyint':
                return 'integer';

            case 'varchar':
                return 'string';

            case 'text':
                return 'string';

            case 'bit':
                return 'boolean';

            default:
                return $value;
        }
    }

}

?>
