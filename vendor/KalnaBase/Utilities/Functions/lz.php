<?php

/**
 * Description of lz
 *
 * Appending leading one zero
 * 
 * @param $num number The number
 *
 * @return string The number with leading zero. 
 * 
 */
function lz($num) {
    return (strlen($num) < 2) ? "0{$num}" : $num;
}

?>
