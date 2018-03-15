<?php

namespace Zefire\Helpers;

class Str
{
	/**
     * Converts a string to camel case.
     *
     * @param  string $value
     * @return string
     */
    public static function camel($value)
    {
        return lcfirst(static::studly($value));
    }
    /**
     * Converts a string to kebab case.
     *
     * @param  string $value
     * @return string
     */
	public static function kebab($value)
    {
        return static::snake($value, '-');
    }
    /**
     * Converts a string to snake case.
     *
     * @param  string $value
     * @param  string $delimiter
     * @return string
     */
	public static function snake($value, $delimiter = '_')
    {
        $key = $value;
        if (! ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', $value);
            $value = mb_strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1'.$delimiter, $value), 'UTF-8');
        }
        return $value;
    }
    /**
     * Converts a string to a studly caps case.
     *
     * @param  string $value
     * @return string
     */
    public static function studly($value)
    {
        $key = $value;
        $value = ucwords(str_replace(['-', '_'], ' ', $value));
        return str_replace(' ', '', $value);
    }
    /**
     * Converts a string to a slug.
     *
     * @param  string $title
     * @param  string $separator
     * @return string
     */
    public static function slugify($title, $separator = '-')
    {
        $flip = $separator == '-' ? '_' : '-';
        $title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);
        $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', mb_strtolower($title));
		$title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);
        return trim($title, $separator);
    }
}