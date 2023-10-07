<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Http\Controller\Base;

use Nette\DI\Container;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

interface IController
{
    /**
     * @param string $path
     * @param array<string, mixed> $params
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render(string $path, array $params = []): Response;
    
    /**
     * @param array<string|int,mixed> $data
     * @param int $code
     * @return Response
     */
    public function json(array $data = [], int $code = 200): Response;
    
    /**
     * @param array<string|int,mixed> $messages
     * @param int $code
     * @return Response
     */
    public function error(array $messages, int $code = 400): Response;
    
    public function sendFile(File $file): Response;
    
    public function sendFileContent(string $content, string $fileName): Response;
    
    public function redirectUrl(string $url): RedirectResponse;
    
    /**
     * @param string $route
     * @param array<string,string|int> $params
     * @return RedirectResponse
     */
    public function redirect(string $route, array $params = []): RedirectResponse;
    
    public function __inject(Container $container): void;
}