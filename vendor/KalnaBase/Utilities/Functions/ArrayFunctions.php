<?php

namespace KalnaBase\Utilities\Functions;

class ArrayFunctions {

    function __construct() {
        
    }

    /**
     * Description of arrayTrim
     *
     * @author:     Claus Hjort Bube <chb@kalna.dk>
     * @org_author: 
     * @created:    24-11-2013
     * @return:     array               //string, int, decimal, array, function
     * 
     * @name:       arrayTrim
     * @version:    0.1
     * @desc:       function for trimming an array, returns an array 
     * 
     * @param
     * $array are required, and passed as reference
     * $key are required, and passed as value
     * $value are required, and passed as value
     * $type are optionel, and passed as value (filter or exclude)
     * 
     * @example
     * $m = arrayTrim($array,                          // array
     *                 'favorites_yn',                  // key
     *                 1,                               // value
     *                 filter);                         // type
     */

    /**
     * 
     * @param array $array required
     * @param mixed $key required
     * @param mixed $value required
     * @param string $type optionel
     * @return array
     */
    public static function arrayTrim($array, $key, $value, $type = 'filter') {
        if (count($array)) {
            foreach ($array as $i => $element) {
                if ((is_array($element) && $element[$key] == $value) || (is_object($element) && $element->$key == $value)) {
                    $temp[] = $element;
                    unset($array[$i]);
                }
            }
            if ($type == 'filter') {
                return $temp;
            } else {
                return $array;
            }
        } else {
            return array(); // [];
        }
    }

    public static function arrayTrim2($array, $key, $value, $type = 'filter') {
        if (count($array) == 1 && count($array[0]) > 0) {
            return arrayTrim2($array[0]);
        } elseif (count($array)) {
            foreach ($array as $i => $element) {
                if ((is_array($element) && $element[$key] == $value) || (is_object($element) && $element->$key == $value)) {
                    $temp[] = $element;
                    unset($array[$i]);
                }
            }
            if ($type == 'filter') {
                return $temp;
            } else {
                return $array;
            }
        } else {
            return array(); // [];
        }
    }

    public static function arrayTrim3($array, $key, $value, $type = 'filter') {
        foreach ($array as $i => $element) {
            if ((is_array($element) && $element[$key] == $value) || (is_object($element) && $element->$key == $value)) {
                $temp[] = $element;
                unset($array[$i]);
            }
        }
        if ($type == 'filter') {
            return $temp;
        } else {
            return $array;
        }
    }

    /**
     * Returning the first array, where $key og $value matches
     * @param array $array
     * @param mixed $key //int or string
     * @param mixed $value
     * @return mixed 
     */
    public static function arrayFetch($array, $key, $value) {
        if (!empty($array)) {
            foreach ($array as $i => $element) {
                if ((is_array($element) && $element[$key] == $value) || (is_object($element) && $element->$key == $value)) {
                    return $element;
                }
            }
        } else {
            return null;
        }
    }

    public static function arrayFillIn($newValues, $defaultValues) {
        self::arrayFillInInternal($newValues, $defaultValues);
        return $defaultValues;
    }

    private static function arrayFillInInternal($newValues, &$defaultValues) {
        foreach ($newValues as $key => $value) {
            if (is_array($value)) {
                self::arrayFillInInternal($newValues[$key], $defaultValues[$key]);
            } else {
                $defaultValues[$key] = $value;
            }
        }
    }

    /**
     * 
     * @param array $array required
     * @return string
     */
    public static function arrayStringify(array $array, $glue = ',') {
        foreach ($array as $key => $value) {
            $string .= "'" . $value . "'" . $glue;
        }
        return trim($string, ',');
    }

    public static function toCamelCase($string) {
        $string_ = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
        return lcfirst($string_);
    }

    public static function toUnderscore($string) {
        return strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1_$2", $string));
    }

// http://stackoverflow.com/a/1444929/632495
    public static function transformKeys($transform, &$array) {
        foreach (array_keys($array) as $key) {
            # Working with references here to avoid copying the value.
            $value = &$array[$key];
            unset($array[$key]);
            # This is what you actually want to do with your keys:
            #  - remove exclamation marks at the front
            #  - camelCase to snake_case
            $transformedKey = call_user_func($transform, $key);
            # Work recursively
            if (is_array($value)) {
                self::transformKeys($transform, $value);
            }
            # Store with new key
            $array[$transformedKey] = $value;
            # Do not forget to unset references!
            unset($value);
        }
    }

    public static function keysToCamelCase($array) {
        self::transformKeys(['self', 'toCamelCase'], $array);
        return $array;
    }

    public static function keysToUnderscore($array) {
        self::transformKeys(['self', 'toUnderscore'], $array);
        return $array;
    }

    public static function transformValues($transform, &$array, $keyName) {
        foreach (array_keys($array) as $key) {
            # Working with references here to avoid copying the value.
            $value = &$array[$key];
//            print_r($value);
            if (!is_array($value) && $keyName = $key) {
//            print_r($value . '=>' . $instruction[$value] . "\n");
                if ($transform === 'toCamelCase') {
                    $newValue = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $value))));
                } elseif ($transform === 'toPascalCase') {
                    $newValue = ucfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $value))));
                } elseif ($transform === 'toUnderscore') {
                    $newValue = self::toUnderscore($value);
                }
            } else {
//            print_r($value . '=>' . 'no match' . "\n");
                $newValue = $value;
            }
            # Work recursively
            if (!is_array($value)) {
                # Store with new value
                $array[$key] = $newValue;
            } else {
                self::transformValues($transform, $value, $keyName);
            }
//            print_r($value);
            # Do not forget to unset references!
            unset($value);
        }
    }

    public static function valuesToCamelCase($array, $keyName) {
        self::transformValues('toCamelCase', $array, $keyName);
        return $array;
    }

    public static function valuesToPascalCase($array, $keyName) {
        self::transformValues('toPascalCase', $array, $keyName);
        return $array;
    }

    public static function valuesToUnderscore($array, $keyName) {
        self::transformValues('toUnderscore', $array, $keyName);
        return $array;
    }

    public static function replaceKeysInt($instruction, &$array) {
//        print_r($array);
        foreach (array_keys($array) as $key) {
//            print_r($key . '=>' . $instruction[$key] . "\n");
            # Working with references here to avoid copying the value.
            $value = &$array[$key];
            unset($array[$key]);
            if (!empty($instruction[$key])) {
//                print_r($instruction[$key]);
                $transformedKey = $instruction[$key];
            } else {
                $transformedKey = $key;
            }
            # Work recursively
            if (is_array($value)) {
                self::replaceKeysInt($instruction, $value);
            }
            # Store with new key
            $array[$transformedKey] = $value;
            # Do not forget to unset references!
            unset($value);
        }
    }

    public static function replaceKeys($instruction, $array) {
        self::replaceKeysInt($instruction, $array);
        return $array;
    }

    public static function replaceValuesInt($instruction, &$array) {
//        print_r($array);
        foreach (array_keys($array) as $key) {
            # Working with references here to avoid copying the value.
            $value = &$array[$key];
            if (!is_array($value) && !empty($instruction[$value])) {
//            print_r($value . '=>' . $instruction[$value] . "\n");
                $newValue = $instruction[$value];
            } else {
//            print_r($value . '=>' . 'no match' . "\n");
                $newValue = $value;
            }
            # Work recursively
            if (!is_array($value)) {
                # Store with new value
                $array[$key] = $newValue;
            } else {
                self::replaceValuesInt($instruction, $value);
            }
            # Do not forget to unset references!
            unset($value);
//            print_r($key);
//            print_r($array);
//            print_r("\n");
        }
    }

    public static function replaceValues($instruction, $array) {
        self::replaceValuesInt($instruction, $array);
        return $array;
    }

    public static function castValuesInt($instruction, &$array) {
//        print_r($instruction);
        foreach (array_keys($array) as $key) {
            # Working with references here to avoid copying the value.
            $value = &$array[$key];
//            print_r($key . '=>' . $value.'<br>' . "\n");
//            if (is_numeric($value)) {
//                print_r('is_integer => true '.'<br>' . "\n");
//            } else {
//                print_r('is_integer => false '.'<br>' . "\n");
//            }
            if (!is_array($value) && !empty($instruction['boolean']) && in_array($key, $instruction['boolean']) && in_array($value, ['true', 'false'])) {
//            print_r($key . '=>' . $value . '=>' . 'boolean<br>' . "\n");
                if ($value === 'false') {
                    $newValue = FALSE;
                } else {
                    $newValue = TRUE;
                }
//                $newValue = (boolean) $value;
            } elseif (!is_array($value) && !empty($instruction['integer']) && in_array($key, $instruction['integer']) && is_numeric($value)) {
//            print_r($key . '=>' . $value . '=>' . 'integer<br>' . "\n");
                $newValue = (integer) $value;
            } elseif (!is_array($value) && !empty($instruction['float']) && in_array($key, $instruction['float']) && is_float($value)) {
//            print_r($key . '=>' . $value . '=>' . 'float<br>' . "\n");
                $newValue = (float) $value;
            } else {
//            print_r($value . '=>' . 'no match' . "\n");
                $newValue = $value;
            }
            # Work recursively
            if (!is_array($value)) {
                # Store with new value
                $array[$key] = $newValue;
            } else {
                self::castValuesInt($instruction, $value);
            }
            # Do not forget to unset references!
            unset($value);
//            print_r($key);
//            print_r($array);
//            print_r("\n");
        }
    }

    public static function castValues($instruction, $array) {
        self::castValuesInt($instruction, $array);
        return $array;
    }

    public static function removeEmptyKeysInt(&$array) {
        foreach (array_keys($array) as $key) {
            # Working with references here to avoid copying the value.
            $value = &$array[$key];
            unset($array[$key]);
            print_r($key . '=>' . $value . '<br>' . "\n");
            if (!empty($value)) {
//                print_r($instruction[$key]);
                $array[$key] = $value;
            }
            # Work recursively
            if (is_array($value)) {
                self::removeEmptyKeysInt($value);
            }
            # Do not forget to unset references!
            unset($value);
        }
    }

    public static function removeEmptyKeys($array) {
        self::removeEmptyKeysInt($array);
        return $array;
    }

}
