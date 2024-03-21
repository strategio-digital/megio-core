<?php
declare(strict_types=1);

namespace Megio\Helper;

class ArrayMove
{
    /**
     * @param array<string, mixed> $array
     * @param string $key
     * @return array<string, mixed>
     */
    public static function moveToEnd(array $array, string $key): array
    {
        if (!array_key_exists($key, $array)) {
            return $array;
        }
        $value = $array[$key];
        unset($array[$key]);
        
        $array[$key] = $value;
        return $array;
    }
    
    /**
     * @param array<string, mixed> $array
     * @param string $key
     * @return array<string, mixed>
     */
    public static function moveToStart(array $array, string $key): array
    {
        if (!array_key_exists($key, $array)) {
            return $array;
        }
        
        $value = $array[$key];
        unset($array[$key]);
        return [$key => $value] + $array;
    }
}