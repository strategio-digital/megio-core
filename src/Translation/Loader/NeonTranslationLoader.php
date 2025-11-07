<?php

declare(strict_types=1);

namespace Megio\Translation\Loader;

use Megio\Helper\Path;
use Megio\Translation\Parser\NeonParser;
use Nette\Neon\Exception;
use Symfony\Component\Finder\Finder;

use function array_merge;
use function is_dir;

final readonly class NeonTranslationLoader
{
    public function __construct(
        private NeonParser $neonParser,
    ) {}

    /**
     * Load translations from .neon files for given locale
     *
     * @throws Exception
     *
     * @return array<string, string>
     */
    public function load(string $locale): array
    {
        $messages = [];
        $appDir = Path::appDir();

        if (is_dir($appDir) === false) {
            return $messages;
        }

        // Find all .locale.{locale}.neon files
        // Example: app/User/user.locale.cs_CZ.neon
        $finder = new Finder();
        $finder->files()
            ->in($appDir)
            ->name("*.locale.{$locale}.neon");

        foreach ($finder as $file) {
            $parsed = $this->neonParser->parseFileToFlatten($file->getRealPath());
            $messages = array_merge($messages, $parsed);
        }

        return $messages;
    }

    /**
     * Load translations from specific directory for given locale
     *
     * @throws Exception
     *
     * @return array<string, string>
     */
    public function loadFromDirectory(string $directory, string $locale): array
    {
        $messages = [];

        if (is_dir($directory) === false) {
            return $messages;
        }

        $finder = new Finder();
        $finder->files()
            ->in($directory)
            ->name("*.locale.{$locale}.neon");

        foreach ($finder as $file) {
            $parsed = $this->neonParser->parseFileToFlatten($file->getRealPath());
            $messages = array_merge($messages, $parsed);
        }

        return $messages;
    }

    /**
     * Load translations from specific file
     *
     * @throws Exception
     *
     * @return array<string, string>
     */
    public function loadFromFile(string $filePath): array
    {
        return $this->neonParser->parseFileToFlatten($filePath);
    }
}
