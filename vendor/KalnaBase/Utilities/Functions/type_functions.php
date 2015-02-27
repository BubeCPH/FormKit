<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of type_functions
 *
 * @author:     Claus Hjort Bube <chb@kalna.dk>
 * @org_author: 
 * @created:    29-01-2014
 * @return:     ?               //string, int, decimal, array, function
 * 
 * @name:       type_functions
 * @version:    0.1
 * @desc:       function for 
 * 
 * @param
 * $foo are required
 * $bar are optional
 * 
 * @example
 * $m = type_functions( "hello there",                           // foo
 *               "how are you?");                         // bar
 */
function type_is_int($value) {
    return intval($value) == $value;
}
?>
