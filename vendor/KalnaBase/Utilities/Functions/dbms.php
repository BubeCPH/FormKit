<?php

/**
 * Description of dbms
 *
 * A function for returning debugging messages
 * 
 * @author:     chb
 * 
 * @return:     
 * string
 * 
 * @version:    0.1
 * 
 * @param
 * $message required Value to be returned in debug message
 * @param
 * $type optional Value can be 'js' or 'php'
 * 
 */
function dbms($message,$type = 'php') {
    $type = strtolower($type);
    if($type == 'js'){
        echo "<script lang=\"javascript\">alert('".$message."')</script>";
    }
    if($type == 'php'){
        $_SESSION['dbms'] .= $message."<br />";
    }
}
?>
