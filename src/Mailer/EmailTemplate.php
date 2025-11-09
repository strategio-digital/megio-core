<?php
declare(strict_types=1);

namespace Megio\Mailer;

use InvalidArgumentException;
use Megio\Extension\Latte\Helper\Vite;
use Megio\Helper\EnvConvertor;

use function array_key_exists;

final readonly class EmailTemplate
{
    /**
     * @param array<string, mixed> $params
     */
    public function __construct(
        private string $file,
        private string $subject,
        private array $params = [],
        private ?string $preHeader = null,
        private string $locale = 'cs',
    ) {}

    public function getParam(string $name): mixed
    {
        if (array_key_exists($name, $this->params) === false) {
            throw new InvalidArgumentException("Param '$name' not found in template '$this->file'.");
        }

        return $this->params[$name];
    }

    public function getAppName(): string
    {
        return EnvConvertor::toString($_ENV['APP_NAME']);
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getPreHeader(): ?string
    {
        return $this->preHeader;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return array<string, string>
     */
    public function getFooterParams(): array
    {
        return [
            'signature_img' => $this->getAssetLink('assets/img/mail-logo.png'),
            'signature_title' => EnvConvertor::toString($_ENV['MAIL_SIGNATURE_TITLE']),
            'signature_text' => EnvConvertor::toString($_ENV['MAIL_SIGNATURE_TEXT']),
        ];
    }

    public function getAssetLink(string $path): string
    {
        return $this->getAppUrl() . new Vite()->resolveSource($path);
    }

    public function getAppUrl(): string
    {
        return EnvConvertor::toString($_ENV['APP_URL']);
    }
}
