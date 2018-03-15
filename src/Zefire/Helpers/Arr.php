<?php

namespace Zefire\Helpers;

class Arr
{
	/**
     * Gets a value from an array using dot notation.
     *
     * @param  string $key
     * @param  mixed  $array
     * @return mixed
     */
    public static function get($key, $array = false)
    {
        if ($array !== false) {
            if (array_key_exists($key, $array)) {
                return $array[$key];
            } else {
                foreach (explode('.', $key) as $segment) {
                    if (is_array($array) && array_key_exists($segment, $array)) {
                        $array = $array[$segment];
                    } else {
                        return null;
                    }
                }    
                return $array;
            }   
        } else {
            $split = explode('.', $key);
            if (!file_exists(\App::configPath() . $split[0] . '.php')) {
                return null;
            }
            $array = include \App::configPath() . $split[0] . '.php';
            unset($split[0]);
            if (empty($split)) {
                return $array;
            } else {
                $split = array_values($split);
                $key = implode('.', $split);
                return static::get($key, $array);    
            }            
        }        
    }
    /**
     * Converts an object to an array.
     *
     * @param  string $object
     * @return array
     */
    public static function objectToArray($object)
    {
        if (is_object($object)) {
            return json_decode(json_encode($object), true);    
        } else {
            throw new \Exception('Input sould be an object, ' . gettype($object) . ' given');
        }        
    }
    /**
     * Converts an array to an object.
     *
     * @param  array $array
     * @return \stdClass
     */
    public static function arrayToObject($array)
    {
        if (is_array($array)) {
            return (object) $array;        
        } else {
            throw new \Exception('Input sould be an array, ' . gettype($array) . ' given');
        }        
    }
    /**
     * Returns the difference between to associative arrays.
     *
     * @param  array $array1
     * @param  array $array2
     * @return array
     */
    public static function arrayDiffAssocRecursive($array1, $array2)
    {
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key])) {
                    $difference[$key] = $value;
                } else if (!is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = self::arrayDiffAssocRecursive($value, $array2[$key]);
                    if ($new_diff !== false) {
                        $difference[$key] = $new_diff;
                    }
                }
            } else if (!isset($array2[$key]) || $array2[$key] != $value) {
                $difference[$key] = $value;
            }
        }
        return !isset($difference) ? 0 : $difference;
    }
    /**
     * Searches a value in an array.
     *
     * @param  mixed $needle
     * @param  array $haystack
     * @param  bool  $withParent
     * @return mixed
     */
    public static function recursiveArraySearch($needle, $haystack, $withParent = false)
    {
        foreach ($haystack as $key => $value) {
            $currentKey = $key;
            if ($needle === $value || (is_array($value) && self::recursiveArraySearch($needle, $value) !== false)) {
                if ($withParent === true) {
                    return ['currentKey' => $currentKey, 'parentKey' => $key];
                } else {
                    return $currentKey;
                }
            }
        }
        return false;
    }
    /**
     * Sorts keys from an array.
     *
     * @param  array  $array
     * @param  string $order
     * @param  bool   $json
     * @return mixed
     */
    public static function sortArrayKeys($array, $order = 'asc', $json = false)
    {
        if ($json === true) {
            $array = json_decode($array, true);
        }
        switch ($order) {
            case 'asc':
                ksort($array);
                break;
            case 'desc':
                krsort($array);
                break;
        }
        return ($json === true) ? json_encode($array) : $array;
    }
}