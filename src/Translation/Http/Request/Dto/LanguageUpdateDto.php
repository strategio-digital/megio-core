<?php

declare(strict_types=1);

namespace Megio\Translation\Http\Request\Dto;

use Symfony\Component\Validator\Constraints as Assert;

readonly class LanguageUpdateDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $id,
        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 50)]
        public string $name,
        #[Assert\Type('bool')]
        public bool $isDefault,
        #[Assert\Type('bool')]
        public bool $isEnabled,
    ) {}
}
