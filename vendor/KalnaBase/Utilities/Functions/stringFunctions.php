<?php

namespace KalnaBase\Utilities\Functions;

/**
 * Description of stringFunctions
 *
 * @author:     Claus Hjort Bube <cb at kalna.dk>
 * @org_author: 
 * @created:    21-02-2015
 * @return:     ?               //string, int, decimal, array, function
 * 
 * @name:       stringFunctions
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
class stringFunctions {

    const CAMEL_CASE = 1;
    const PASCAL_CASE = 2;
    const UNDER_SCORE = 3;
    const UPPER_CASE = 4;

    static function transform($string, $format) {
        $string = trim($string);
        switch ($format) {
            case 1:
                $string = strtolower($string);
                $func = create_function('$c', 'return strtoupper($c[1]);');
                return preg_replace_callback('/_([a-z])/', $func, $string);
            case 2:
                $string[0] = strtoupper($string[0]);
                $func = create_function('$c', 'return strtoupper($c[1]);');
                return preg_replace_callback('/_([a-z])/', $func, $string);
            case 3:
                $string[0] = strtolower($string[0]);
                $func = create_function('$c', 'return "_" . strtolower($c[1]);');
                return preg_replace_callback('/([A-Z])/', $func, $string);
            case 4:
//                https://github.com/zendframework/Component_ZendFilter/blob/master/Word/CamelCaseToSeparator.php
//                $pattern = array('#(?<=(?:[A-Z]))([A-Z]+)([A-Z][a-z])#', '#(?<=(?:[a-z0-9]))([A-Z])#');
//                $replacement = array('\1' . '_' . '\2', '_' . '\1');
//                return preg_replace($pattern, $replacement, $string);
                $string[0] = strtolower($string[0]);
                $func = create_function('$c', 'return "_" . strtolower($c[1]);');
                return strtoupper(preg_replace_callback('/([A-Z])/', $func, $string));

            default:
                return $string;
        }
    }

}
