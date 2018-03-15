<?php

use Zefire\Dumper\Dumper;
use Zefire\Helpers\Str;
use Zefire\Helpers\Arr;
use Zefire\Helpers\Format;

/**
 * Creates a singleton of a given class.
 *
 * @param  string $class
 * @return object
 */
if (!function_exists('singleton')) {
    function singleton($class) {
        return \App::make($class);
    }
}
/**
 * Creates a new instance of a given class.
 *
 * @param  string $class
 * @return object
 */
if (!function_exists('make')) {
    function make($class) {
        return \Factory::make($class);
    }
}
/**
 * Boots a model.
 *
 * @param  string $model
 * @return object
 */
if (!function_exists('model')) {
    function model($model) {
        return \Factory::make($model);
    }
}
/**
 * Dumps a value and dies for debug purposes.
 *
 * @param  array $args
 * @return void
 */
if (!function_exists('dd')) {
    function dd(...$args) {
        foreach ($args as $x) {
            (new Dumper)->dump($x);
        }
        die(1);        
    }
}
/**
 * Dumps a value for debug purposes.
 *
 * @param  array $args
 * @return void
 */
if (!function_exists('dump')) {
    function dump(...$args) {
        foreach ($args as $x) {
            (new Dumper)->dump($x);
        }   
    }
}
/**
 * Converts a string to camel case.
 *
 * @param  string $value
 * @return string
 */
if (!function_exists('camel_case')) {
    function camel_case($value) {
        return Str::camel($value);
    }
}
/**
 * Converts a string to kebab case.
 *
 * @param  string $value
 * @return string
 */
if (!function_exists('kebab_case')) {
    function kebab_case($value) {
        return Str::kebab($value);
    }
}
/**
 * Converts a string to snake case.
 *
 * @param  string $value
 * @return string
 */
if (!function_exists('snake_case')) {
    function snake_case($value) {
        return Str::snake($value);
    }
}
/**
 * Converts a string to slug format.
 *
 * @param  string $value
 * @return string
 */
if (!function_exists('slugify')) {
    function slugify($value) {
        return Str::slugify($value);
    }
}
/**
 * Gets runtime.
 *
 * @return float
 */
if (!function_exists('runtime')) {
    function runtime() {
        return \App::runtime();
    }
}
/**
 * Retrieve config settings using dot notation.
 *
 * @param  string $value
 * @return mixed
 */
if (!function_exists('config')) {
    function config($value) {
        return \App::config($value);
    }
}
/**
 * Retrieve translation using dot notation.
 *
 * @param  string $value
 * @return string
 */
if (!function_exists('translate')) {
    function translate($value) {
        return \Translate::get($value);
    }
}
/**
 * Converts an object to an array.
 *
 * @param  string $value
 * @return array
 */
if (!function_exists('object_to_array')) {
    function object_to_array($value) {
        return Arr::objectToArray($value);
    }
}
/**
 * Converts an array to an object.
 *
 * @param  array $value
 * @return \stdClass
 */
if (!function_exists('array_to_object')) {
    function array_to_object($value) {
        return Arr::arrayToObject($value);
    }
}
/**
 * Returns the difference between to associative arrays.
 *
 * @param  array $array1
 * @param  array $array2
 * @return array
 */
if (!function_exists('array_diff_assoc_recursive')) {
    function array_diff_assoc_recursive($array1, $array2) {
        return Arr::arrayDiffAssocRecursive($array1, $array2);
    }
}
/**
 * Searches a value in an array.
 *
 * @param  mixed $needle
 * @param  array $haystack
 * @param  bool  $withParent
 * @return mixed
 */
if (!function_exists('recursive_array_search')) {
    function recursive_array_search($needle, $haystack, $withParent = false) {
        return Arr::recursiveArraySearch($needle, $haystack, $withParent);
    }
}
/**
 * Sorts keys from an array.
 *
 * @param  array  $array
 * @param  string $order
 * @param  bool   $json
 * @return mixed
 */
if (!function_exists('sort_array_keys')) {
    function sort_array_keys($array, $order = 'asc', $json = false) {
        return Arr::sortArrayKeys($array, $order, $json);
    }
}
/**
 * Formats a value into bytes.
 *
 * @param  mixed  $bytes
 * @param  string $spacer
 * @param  int    $precision
 * @param  int    $divisor
 * @return string
 */
if (!function_exists('to_bytes')) {
    function to_bytes($bytes, $spacer = '', $precision = 2, $divisor = 1024) {
        return Format::format($bytes, 'bytes', $spacer, $precision, $divisor);
    }
}
/**
 * Formats a value into bits.
 *
 * @param  mixed  $bits
 * @param  string $spacer
 * @param  int    $precision
 * @param  int    $divisor
 * @return string
 */
if (!function_exists('to_bits')) {
    function to_bits($bits, $spacer = '', $precision = 2, $divisor = 1024) {
        return Format::format($bits, 'bits', $spacer, $precision, $divisor);
    }
}
/**
     * Formats a value into currency.
     *
     * @param  mixed  $val
     * @param  string $currency
     * @param  string $thousands
     * @param  bool   $hideDecimals
     * @return string
     */
if (!function_exists('to_currency')) {
    function to_currency($val, $currency = '£', $thousands = ',', $hideDecimals = false) {
        return Format::formatCurrency($val, $currency, $thousands, $hideDecimals);
    }
}
/**
 * Formats an integer.
 *
 * @param  int    $val
 * @param  string $thousands
 * @return string
 */
if (!function_exists('to_int')) {
    function to_int($val, $thousands = ',') {
        return Format::formatInt($val, $thousands);
    }
}
/**
 * Formats a float.
 *
 * @param  float  $val
 * @param  string $thousands
 * @return string
 */
if (!function_exists('to_float')) {
    function to_float($val, $thousands = ',') {
        return Format::formatFloat($val, $thousands);
    }
}