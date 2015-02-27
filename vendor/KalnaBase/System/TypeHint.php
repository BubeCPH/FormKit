<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TypeHint
 *
 * @author:     Claus Hjort Bube <chb@kalna.dk>
 * @org_author: 
 * @created:    22-11-2013
 * @return:     ?               //string, int, decimal, array, function
 * 
 * @name:       TypeHint
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
define('TYPEHINT_PCRE', '/^Argument (\d)+ passed to (?:(\w+)::)?(\w+)\(\) must be an instance of (\w+), (\w+) given/');

class Typehint {

    private static $Typehints = array(
        'boolean' => 'is_bool',
        'integer' => 'is_int',
        'float' => 'is_float',
        'string' => 'is_string',
        'resrouce' => 'is_resource'
    );

    private function __Constrct() {
        
    }

    public static function initializeHandler() {

        set_error_handler('Typehint::handleTypehint');

        return TRUE;
    }

    private static function getTypehintedArgument($ThBackTrace, $ThFunction, $ThArgIndex, &$ThArgValue) {

        foreach ($ThBackTrace as $ThTrace) {

            // Match the function; Note we could do more defensive error checking.
            if (isset($ThTrace['function']) && $ThTrace['function'] == $ThFunction) {

                $ThArgValue = $ThTrace['args'][$ThArgIndex - 1];

                return TRUE;
            }
        }

        return FALSE;
    }

    public static function handleTypehint($ErrLevel, $ErrMessage) {

        if ($ErrLevel == E_RECOVERABLE_ERROR) {

            if (preg_match(TYPEHINT_PCRE, $ErrMessage, $ErrMatches)) {

                list($ErrMatch, $ThArgIndex, $ThClass, $ThFunction, $ThHint, $ThType) = $ErrMatches;

                if (isset(self::$Typehints[$ThHint])) {

                    $ThBacktrace = debug_backtrace();
                    $ThArgValue = NULL;

                    if (self::getTypehintedArgument($ThBacktrace, $ThFunction, $ThArgIndex, $ThArgValue)) {

                        if (call_user_func(self::$Typehints[$ThHint], $ThArgValue)) {

                            return TRUE;
                        }
                    }
                }
            }
        }

        return FALSE;
    }

}