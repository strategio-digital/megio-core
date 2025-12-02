<?php

declare(strict_types=1);

namespace Megio\Translation\Loader;

use Megio\Database\EntityManager;

final readonly class DatabaseTranslationLoader
{
    public function __construct(
        private EntityManager $em,
    ) {}

    /**
     * Load translations from database for given locale
     *
     * @return array<string, string>
     */
    public function load(string $posix): array
    {
        $messages = [];

        $translations = $this->em->getTranslationRepo()->findByLanguagePosix($posix);

        foreach ($translations as $translation) {
            $fullKey = $translation->getDomain() . '.' . $translation->getKey();
            $messages[$fullKey] = $translation->getValue();
        }

        return $messages;
    }
}
