<?php
declare(strict_types=1);

namespace Megio\Http\Controller\Base;

use Nette\DI\Container;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

interface IController
{
    /**
     * @param array<string, mixed> $params
     */
    public function render(string $path, array $params = []): Response;

    /**
     * @param array<int|string,mixed> $data
     */
    public function json(array $data = [], int $code = 200): Response;

    /**
     * @param array<int|string,mixed> $messages
     */
    public function error(array $messages, int $code = 400): Response;

    public function sendFile(File $file): Response;

    public function sendFileContent(string $content, string $fileName): Response;

    public function redirectUrl(string $url): RedirectResponse;

    /**
     * @param array<string,int|string> $params
     */
    public function redirect(string $route, array $params = []): RedirectResponse;

    public function __inject(Container $container): void;
}
