<?php
declare(strict_types=1);

namespace Megio\Http\Controller\Base;

use Latte\Engine;
use Megio\Debugger\ResponseFormatter;
use Nette\DI\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;

abstract class Controller implements IController
{
    protected EventDispatcher $dispatcher;

    private ResponseFormatter $formatter;

    private UrlGenerator $urlGenerator;

    private Engine $latte;

    public function __inject(Container $container): void
    {
        $this->formatter = $container->getByType(ResponseFormatter::class);
        $this->urlGenerator = $container->getByType(UrlGenerator::class);
        $this->latte = $container->getByType(Engine::class);
        $this->dispatcher = $container->getByType(EventDispatcher::class);
    }

    /**
     * @param array<string, mixed> $params
     */
    public function render(
        string $path,
        array $params = [],
    ): Response {
        $html = $this->latte->renderToString($path, $params);
        return new Response($html, 200, ['content-type' => 'text/html']);
    }

    /**
     * @param array<int|string,mixed> $data
     */
    public function json(
        array $data = [],
        int $code = 200,
    ): Response {
        $data = $this->formatter->formatResponseData($data);
        return new JsonResponse($data, $code);
    }

    /**
     * @param array<int|string,mixed> $messages
     */
    public function error(
        array $messages,
        int $code = 400,
    ): Response {
        $data = ['errors' => $messages];
        $data = $this->formatter->formatResponseData($data);

        return new JsonResponse($data, $code);
    }

    public function sendFile(File $file): Response
    {
        $disposition = HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $file->getFilename());
        return new Response($file->getContent(), 200, ['Content-Disposition' => $disposition]);
    }

    public function sendFileContent(
        string $content,
        string $fileName,
    ): Response {
        $disposition = HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $fileName);
        return new Response($content, 200, ['Content-Disposition' => $disposition]);
    }

    public function redirectUrl(string $url): RedirectResponse
    {
        return new RedirectResponse($url);
    }

    /**
     * @param array<string,int|string> $params
     */
    public function redirect(
        string $route,
        array $params = [],
    ): RedirectResponse {
        $url = $this->urlGenerator->generate($route, $params);
        return new RedirectResponse($url);
    }
}
