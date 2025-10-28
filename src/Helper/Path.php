<?php
declare(strict_types=1);

namespace Megio\Helper;

class Path
{
    private static string $projectPath;

    public static function setProjectPath(string $projectPath): void
    {
        self::$projectPath = $projectPath;
    }

    public static function appDir(): string
    {
        return self::$projectPath . '/app';
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

    public static function wwwTempDir(): string
    {
        return self::$projectPath . '/www/temp';
    }

    public static function configDir(): string
    {
        return self::$projectPath . '/config';
    }

    public static function viewDir(): string
    {
        return self::$projectPath . '/view';
    }

    public static function routerDir(): string
    {
        return self::$projectPath . '/router';
    }

    public static function megioVendorDir(): string
    {
        return __DIR__ . '/../../';
    }
}
