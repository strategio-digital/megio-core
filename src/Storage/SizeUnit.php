<?php
declare(strict_types=1);

namespace Megio\Storage;

enum SizeUnit: string
{
    case Bytes = 'B';
    case KiloBytes = 'KB';
    case MegaBytes = 'MB';
    case GigaBytes = 'GB';
    case TeraBytes = 'TB';
    case PetaBytes = 'PB';
    
    public static function fromPhpIniAlias(string $unit): self
    {
        $unit = strtoupper($unit);
        
        return match ($unit) {
            'B' => self::Bytes,
            'K' => self::KiloBytes,
            'M' => self::MegaBytes,
            'G' => self::GigaBytes,
            'T' => self::TeraBytes,
            'P' => self::PetaBytes,
            default => throw new \InvalidArgumentException("Invalid unit: $unit"),
        };
    }
    
    public static function getIdealUnit(float $size): self
    {
        $unit = self::Bytes;
        
        while ($size >= 1024 && $unit !== self::PetaBytes) {
            $size /= 1024;
            $unit = match ($unit) {
                self::Bytes => self::KiloBytes,
                self::KiloBytes => self::MegaBytes,
                self::MegaBytes => self::GigaBytes,
                self::GigaBytes => self::TeraBytes,
            };
        }
        
        return $unit;
    }
    
    public static function convert(float $size, SizeUnit $sourceUnit, SizeUnit $targetUnit): float
    {
        return $size * $sourceUnit->getMultiplier($targetUnit);
    }
    
    public function getMultiplier(SizeUnit $targetUnit): float
    {
        $units = [
            self::Bytes->value => 1,
            self::KiloBytes->value => 1024,
            self::MegaBytes->value => 1024 ** 2,
            self::GigaBytes->value => 1024 ** 3,
            self::TeraBytes->value => 1024 ** 4,
            self::PetaBytes->value => 1024 ** 5,
        ];
        
        return $units[$this->value] / $units[$targetUnit->value];
    }
}