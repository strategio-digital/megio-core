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
     * @param array<string, string> $headers
     */
    public function render(
        string $path,
        array $params = [],
        int $status = 200,
        array $headers = ['content-type' => 'text/html'],
    ): Response;

    /**
     * @param array<int|string,mixed> $data
     * @param array<string, string> $headers
     */
    public function json(
        array $data = [],
        int $status = 200,
        array $headers = [],
    ): Response;

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     */
    public function error(
        array $data,
        int $status = 400,
        array $headers = [],
    ): Response;

    public function sendFile(File $file): Response;

    public function sendFileContent(
        string $content,
        string $fileName,
    ): Response;

    /**
     * @param array<string, string> $headers
     */
    public function redirectUrl(
        string $url,
        int $status = 302,
        array $headers = [],
    ): RedirectResponse;

    /**
     * @param array<string,int|string> $params
     * @param array<string, string> $headers
     */
    public function redirect(
        string $route,
        array $params = [],
        int $status = 302,
        array $headers = [],
    ): RedirectResponse;

    public function __inject(Container $container): void;
}
