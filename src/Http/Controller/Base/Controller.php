<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Http\Controller\Base;

use Latte\Engine;
use Nette\DI\Container;
use Saas\Debugger\ResponseFormatter;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;

abstract class Controller implements IController
{
    private ResponseFormatter $formatter;
    private UrlGenerator $urlGenerator;
    private Engine $latte;
    protected EventDispatcher $dispatcher;
    
    public function __inject(Container $container): void
    {
        $this->formatter = $container->getByType(ResponseFormatter::class); //@phpstan-ignore-line
        $this->urlGenerator = $container->getByType(UrlGenerator::class); //@phpstan-ignore-line
        $this->latte = $container->getByType(Engine::class); //@phpstan-ignore-line
        $this->dispatcher = $container->getByType(EventDispatcher::class); //@phpstan-ignore-line
    }
    
    /**
     * @param string $path
     * @param array<string, mixed> $params
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render(string $path, array $params = []): Response
    {
        $html = $this->latte->renderToString($path, $params);
        return new Response($html, 200, ['content-type' => 'text/html']);
    }
    
    /**
     * @param array<string|int,mixed> $data
     * @param int $code
     * @return Response
     */
    public function json(array $data = [], int $code = 200): Response
    {
        $data = $this->formatter->formatResponseData($data);
        return new JsonResponse($data, $code);
    }
    
    /**
     * @param array<string|int,mixed> $messages
     * @param int $code
     * @return Response
     */
    public function error(array $messages, int $code = 400): Response
    {
        $data = ['errors' => $messages];
        $data = $this->formatter->formatResponseData($data);
        
        return new JsonResponse($data, $code);
    }
    
    public function sendFile(File $file): Response
    {
        $disposition = HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $file->getFilename());
        return new Response($file->getContent(), 200, ['Content-Disposition' => $disposition]);
    }
    
    public function sendFileContent(string $content, string $fileName): Response
    {
        $disposition = HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $fileName);
        return new Response($content, 200, ['Content-Disposition' => $disposition]);
    }
    
    public function redirectUrl(string $url): RedirectResponse
    {
        return new RedirectResponse($url);
    }
    
    /**
     * @param string $route
     * @param array<string,string|int> $params
     * @return RedirectResponse
     */
    public function redirect(string $route, array $params = []): RedirectResponse
    {
        $url = $this->urlGenerator->generate($route, $params);
        return new RedirectResponse($url);
    }
}
