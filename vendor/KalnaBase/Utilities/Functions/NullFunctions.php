<?php

namespace KalnaBase\Utilities\Functions;

class NullFunctions {

    function __construct() {
        
    }

    /**
     * Description of nvl
     *
     * A function for Oracle's NVL-function (Null Value)
     * 
     * @author:     chb
     * 
     * @return:     
     * string, int, decimal
     * 
     * @version:    0.1
     * 
     * @param
     * $value required Value to be returned normally
     * @param
     * $null_value optional Value to be returned if $value is null or ''
     * 
     */
    function nvl($value, $null_value) {
        if (empty($value))
            return $null_value;
        else
            return $value;
    }

    /**
     * Description of nvl2
     *
     * A function for Oracle's NVL2-function (Null Value)
     * 
     * @author:     chb
     * 
     * @return:     
     * string, int, decimal
     * 
     * @version:    0.1
     * 
     * @param
     * $value required Value to be tested
     * @param
     * $null_value required Value to be returned if $value is null or ''
     * @param
     * $not_null_value required Value to be returned if $value is not null or ''
     * 
     */
    function nvl2($value, $null_value, $not_null_value) {
        if (empty($value))
            return $null_value;
        else
            return $not_null_value;
    }

}
