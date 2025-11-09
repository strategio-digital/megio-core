<?php

declare(strict_types=1);

namespace Megio\Mailer;

use Megio\Extension\Latte\Engine;
use Megio\Helper\Path;

use function ltrim;
use function str_starts_with;
use function strlen;
use function substr;

final readonly class EmailTemplateFactory
{
    private const string VIEW_DIR_PREFIX = 'view/';

    public function __construct(
        private Engine $engine,
    ) {}

    public function render(EmailTemplate $template): string
    {
        $this->engine->setLoader(new EmailTemplateFileLoader(Path::tempDir() . '/latte-mail'));

        return $this->engine->renderToString(
            $this->normalizeFilePath($template),
            ['template' => $template],
        );
    }

    /**
     * Normalize file path - converts absolute paths to relative.
     */
    private function normalizeFilePath(EmailTemplate $template): string
    {
        $viewDir = Path::viewDir();
        $file = $template->getFile();

        // If path starts with viewDir, remove that prefix and prepend 'view/'
        if (str_starts_with($file, $viewDir) === true) {
            $relativePath = ltrim(substr($file, strlen($viewDir)), '/');
            return self::VIEW_DIR_PREFIX . $relativePath;
        }

        // Otherwise return as-is (already relative)
        return $file;
    }
}
