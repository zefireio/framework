<?php

namespace Zefire\Helpers;

class Format
{
    /**
     * Formats a value into bytes or bits.
     *
     * @param  mixed  $bytes
     * @param  string $type
     * @param  string $spacer
     * @param  int    $precision
     * @param  int    $divisor
     * @return string
     */
    public static function format($bytes, $type,  $spacer = '', $precision = 2, $divisor = 1024)
    {
        switch (strtolower($type)) {
            case 'bytes':
                $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                break;
            case 'bits':
                $units = ['bps', 'Kbps', 'Mbps', 'Gbps', 'Tbps'];
                break;
                    case 'megahertz':
                $units = ['MHz', 'GHz', 'THz'];
                break;
        }
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log($divisor));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow($divisor, $pow);
        return round($bytes, $precision) . $spacer . $units[$pow];
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
    public static function formatCurrency($val, $currency = '£', $thousands = ',', $hideDecimals = false)
    {
        $value = htmlentities($currency) . number_format($val, 2, '.', $thousands);
        if ($hideDecimals === true && strstr($value, '.00')) {
            $value = str_replace('.00', '', $value);
        }
        return $value;
    }
    /**
     * Formats an integer.
     *
     * @param  int    $val
     * @param  string $thousands
     * @return string
     */
    public static function formatInt($val, $thousands = ',')
    {
        return number_format($val, 0, '.', $thousands);
    }
    /**
     * Formats a float.
     *
     * @param  float  $val
     * @param  string $thousands
     * @return string
     */
    public static function formatFloat($val, $thousands = ',')
    {
        return number_format($val, 2, '.', $thousands);
    }        
}