<?php

namespace KalnaBase\Utilities;

/**
 * Description of time_to_decimal
 * 
 * Convert time into decimal time.
 *
 * @param string $time The time to convert
 *
 * @return integer The time as a decimal value. 
 * 
 * @example
 * $m = new convert ();
 * $e = $m->time_to_decimal('9:45');
 * echo $e; // prints '9,75'
 */
function format_time($seconds, $format = 'H:i') { // t = seconds, f = separator 
    switch ($format) {
        case 'H:i':
            if ($seconds % 60 >= 30) {
                return sprintf("%02d%s%02d", floor($seconds / 3600), ':', (($seconds / 60) % 60) + 1);
            }
            return sprintf("%02d%s%02d", floor($seconds / 3600), ':', ($seconds / 60) % 60);
        case 'H:i:s':
            return sprintf("%02d%s%02d%s%02d", floor($seconds / 3600), ':', ($seconds / 60) % 60, ':', $seconds % 60);
        case 'dec':
            return sprintf("%.2f", $seconds / 3600);
        default:
            return NULL;
    }
}

/**
 * Description of time_to_decimal
 * 
 * Convert time into decimal time.
 *
 * @param string $time The time to convert
 *
 * @return integer The time as a decimal value. 
 * 
 * @example
 * $m = new convert ();
 * $e = $m->time_to_decimal('9:45');
 * echo $e; // prints '9,75'
 */
function timeToDecimal($time) {
    $timeArr = explode(':', $time);
    //$decTime = ($timeArr[0] * 60) + ($timeArr[1]) + ($timeArr[2] / 60);
    //return $decTime;
    return str_pad(ltrim($timeArr[0], '0'), 1, '0') . '.' . str_pad(round(($timeArr[1] / 60) * 100), 2, '0', 0);
}

/**
 * Description of decimal_to_time
 * 
 * Convert decimal time into time in the format hh:mm:ss
 *
 * @param integer The time as a decimal value.
 *
 * @return string $time The converted time value.
 * 
 * @example
 * $m = new convert ();
 * $e = $m->decimal_to_time(9,75);
 * echo $e; // prints '9:45'
 */
function decimalToTime($decimal) {
    $decimal = str_replace(",", ".", $decimal);
    // start by converting to seconds
    $seconds = $decimal * 3600;
    // we're given hours, so let's get those the easy way
    $hours = floor($decimal);
    // since we've "calculated" hours, let's remove them from the seconds variable
    $seconds -= $hours * 3600;
    // calculate minutes left
    $minutes = floor($seconds / 60);
    // remove those from seconds as well
    $seconds -= $minutes * 60;
    // return the time formatted HH:MM:SS
    //return lz($hours) . ":" . lz($minutes) . ":" . lz($seconds);
    return $hours . ":" . lz($minutes);
}

/**
 * Description of decimal_to_time
 * 
 * Convert decimal time into time in the format hh:mm:ss
 *
 * @param integer The time as a decimal value.
 *
 * @return string $time The converted time value.
 * 
 * @example
 * $m = new convert ();
 * $e = $m->decimal_to_time(9,75);
 * echo $e; // prints '09:45'
 */
function decimalToTime2($decimal) {
    $decimal = str_replace(",", ".", $decimal);
    // start by converting to seconds
    $seconds = $decimal * 3600;
    // we're given hours, so let's get those the easy way
    $hours = floor($decimal);
    // since we've "calculated" hours, let's remove them from the seconds variable
    $seconds -= $hours * 3600;
    // calculate minutes left
    $minutes = floor($seconds / 60);
    // remove those from seconds as well
    $seconds -= $minutes * 60;
    // return the time formatted HH:MM:SS
    //return lz($hours) . ":" . lz($minutes) . ":" . lz($seconds);
    return lz($hours) . ":" . lz($minutes);
}

/**
 * Description of calc_string
 * 
 * calculate math given in string
 *
 * @param string The string containing the math.
 *
 * @return string The calculated string.
 * 
 * @example
 * $c = new convert ();
 * $e = $c->calcString('3+2');
 * echo $e; // prints '5'
 */
function calcString($mathString) {
    eval("\$t = " . $mathString . ";");

    return $t;
}
