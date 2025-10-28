<?php
declare(strict_types=1);

namespace Megio\Storage;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class StorageHelper
{
    public static function isFileSizeOk(UploadedFile $file): bool
    {
        return $file->getSize() <= self::getMaxFileSize();
    }

    public static function getMaxFileSize(SizeUnit $targetUnit = SizeUnit::Bytes): float
    {
        $iniMaxFileSize = (string)ini_get('upload_max_filesize');
        $iniUnit = strtoupper(substr($iniMaxFileSize, -1));
        $iniSize = (int)substr($iniMaxFileSize, 0, -1);

        return SizeUnit::fromPhpIniAlias($iniUnit)->getMultiplier($targetUnit) * $iniSize;
    }
}
