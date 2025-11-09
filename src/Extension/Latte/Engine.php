<?php

declare(strict_types=1);

namespace Megio\Extension\Latte;

use Latte\Engine as BaseEngine;
use Megio\Translation\Translator;

class Engine extends BaseEngine
{
    private ?Translator $translator = null;

    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @param array<string, mixed>|object $params
     */
    public function renderToString(
        string $name,
        array|object $params = [],
        ?string $block = null,
    ): string {
        if (is_array($params) && $this->translator !== null) {
            $params['translator'] = $this->translator;
        }

        return parent::renderToString($name, $params, $block);
    }

    /**
     * @param array<string, mixed>|object $params
     */
    public function render(
        string $name,
        array|object $params = [],
        ?string $block = null,
    ): void {
        if (is_array($params) && $this->translator !== null) {
            $params['translator'] = $this->translator;
        }

        parent::render($name, $params, $block);
    }
}
