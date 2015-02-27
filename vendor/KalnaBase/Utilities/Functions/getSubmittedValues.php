<?php

/**
 * Description of getSubmittedValues
 *
 * A function to get the submitted value
 * 
 * @author:     chb
 * 
 * @return:     
 * string, int, decimal
 * 
 * @version:    0.1
 * 
 * @param
 * $field required Variable containing the submitted value
 * 
 */
function getSubmittedValues($field) {
    $return = filter_input(INPUT_POST, $field, FILTER_SANITIZE_STRING);
    return $return;
}