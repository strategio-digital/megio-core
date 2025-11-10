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
    private const string TEMP_DIR_FOLDER = '/latte-mail';

    public function __construct(
        private Engine $engine,
    ) {}

    public function render(EmailTemplate $template): string
    {
        $this->engine->setLoader(new EmailTemplateFileLoader(Path::tempDir() . self::TEMP_DIR_FOLDER));

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
        $projectRoot = Path::projectRootDir();
        $file = $template->getFile();

        // If path is absolute and starts with project root, make it relative
        if (str_starts_with($file, $projectRoot) === true) {
            return ltrim(substr($file, strlen($projectRoot)), '/');
        }

        // Otherwise return as-is (already relative)
        return $file;
    }
}
