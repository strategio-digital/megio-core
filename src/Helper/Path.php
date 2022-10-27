<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Framework\Helper;

class Path
{
    private static string $projectPath;
    
    public static function setProjectPath(string $projectPath): void
    {
        self::$projectPath = $projectPath;
    }
    
    public static function logDir(): string
    {
        return self::$projectPath . '/log';
    }
    
    public static function tempDir(): string
    {
        return self::$projectPath . '/temp';
    }
    
    public static function wwwDir(): string
    {
        return self::$projectPath . '/www';
    }
    
    public static function srcDir(): string
    {
        return self::$projectPath . '/src';
    }
    
    public static function configDir(): string
    {
        return self::$projectPath . '/config';
    }
    
    public static function frameworkSrcDir(): string
    {
        return self::frameworkDir() . '/src';
    }
    
    public static function frameWorkConfigDir(): string
    {
        return self::frameworkDir() . '/config';
    }
    
    private static function frameworkDir(): string
    {
        return __DIR__ . '/../../';
    }
}