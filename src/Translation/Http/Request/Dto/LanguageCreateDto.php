<?php

declare(strict_types=1);

namespace Megio\Translation\Http\Request\Dto;

use Symfony\Component\Validator\Constraints as Assert;

readonly class LanguageCreateDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^[a-z]{2}_[A-Z]{2}$/', message: 'Code must be POSIX format: cs_CZ, en_US, sk_SK')]
        public string $posix,
        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 50)]
        public string $name,
        #[Assert\Type('bool')]
        public bool $isDefault = false,
        #[Assert\Type('bool')]
        public bool $isEnabled = true,
    ) {}
}
