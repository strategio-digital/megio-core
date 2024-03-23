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
    
    /**
     * @param array<string, mixed> $array
     * @param string $key
     * @param string $afterKey
     * @return array<string, mixed>
     */
    public static function moveAfter(array $array, string $key, string $afterKey): array
    {
        if (!array_key_exists($key, $array)) {
            return $array;
        }
        
        if (!array_key_exists($afterKey, $array)) {
            return $array;
        }
        
        $value = $array[$key];
        unset($array[$key]);
        
        $newArray = [];
        foreach ($array as $k => $v) {
            $newArray[$k] = $v;
            if ($k === $afterKey) {
                $newArray[$key] = $value;
            }
        }
        return $newArray;
    }
    
    /**
     * @param array<string, mixed> $array
     * @param string $key
     * @param string $beforeKey
     * @return array<string, mixed>
     */
    public static function moveBefore(array $array, string $key, string $beforeKey): array
    {
        if (!array_key_exists($key, $array)) {
            return $array;
        }
        
        if (!array_key_exists($beforeKey, $array)) {
            return $array;
        }
        
        $value = $array[$key];
        unset($array[$key]);
        
        $newArray = [];
        foreach ($array as $k => $v) {
            if ($k === $beforeKey) {
                $newArray[$key] = $value;
            }
            $newArray[$k] = $v;
        }
        return $newArray;
    }
}