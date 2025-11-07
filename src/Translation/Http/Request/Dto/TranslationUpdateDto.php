<?php

declare(strict_types=1);

namespace Megio\Translation\Http\Request\Dto;

use Symfony\Component\Validator\Constraints as Assert;

readonly class TranslationUpdateDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $id,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public string $value,
    ) {}
}
