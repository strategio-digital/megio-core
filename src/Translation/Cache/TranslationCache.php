<?php

declare(strict_types=1);

namespace Megio\Translation\Cache;

use Megio\Helper\EnvConvertor;
use Megio\Helper\Path;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

use function is_array;

class TranslationCache
{
    private FilesystemAdapter $filesystem;

    private bool $enabled;

    public function __construct()
    {
        $this->enabled = EnvConvertor::toBool($_ENV['TRANSLATIONS_ENABLE_CACHE']);
        $this->filesystem = new FilesystemAdapter(
            namespace: 'translations',
            defaultLifetime: 0,
            directory: Path::tempDir(),
        );
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws InvalidArgumentException
     */
    public function set(
        string $key,
        array $data,
        int $ttl = 0,
    ): void {
        if ($this->enabled === false) {
            return;
        }

        $this->filesystem->get(
            $key,
            static function (
                ItemInterface $item,
            ) use (
                $data,
                $ttl,
            ) {
                $item->expiresAfter($ttl);
                return $data;
            },
        );
    }

    /**
     * @throws InvalidArgumentException
     *
     * @return array<string, mixed>|null
     */
    public function get(string $key): ?array
    {
        if ($this->enabled === false) {
            return null;
        }

        $item = $this->filesystem->getItem($key);

        if ($item->isHit() === false) {
            return null;
        }

        $value = $item->get();

        return is_array($value) === true ? $value : null;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function has(string $key): bool
    {
        if ($this->enabled === false) {
            return false;
        }

        return $this->filesystem->hasItem($key);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function delete(string $key): void
    {
        $this->filesystem->deleteItem($key);
    }

    public function clear(): void
    {
        $this->filesystem->clear();
    }
}
