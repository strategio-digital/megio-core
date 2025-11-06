<?php
declare(strict_types=1);

namespace Megio\Http\Controller\Base;

use Latte\Engine;
use Megio\Http\Serializer\RequestSerializer;
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

    protected RequestSerializer $requestSerializer;

    private UrlGenerator $urlGenerator;

    private Engine $latte;

    public function __inject(Container $container): void
    {
        $this->requestSerializer = $container->getByType(RequestSerializer::class);
        $this->urlGenerator = $container->getByType(UrlGenerator::class);
        $this->latte = $container->getByType(Engine::class);
        $this->dispatcher = $container->getByType(EventDispatcher::class);
    }

    /**
     * @param array<string, mixed> $params
     * @param array<string, string> $headers
     */
    public function render(
        string $path,
        array $params = [],
        int $status = 200,
        array $headers = ['content-type' => 'text/html'],
    ): Response {
        $html = $this->latte->renderToString($path, $params);
        return new Response($html, $status, $headers);
    }

    /**
     * @param array<int|string,mixed> $data
     * @param array<string, string> $headers
     */
    public function json(
        array $data = [],
        int $status = 200,
        array $headers = [],
    ): Response {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     */
    public function error(
        array $data = [],
        int $status = 400,
        array $headers = [],
    ): Response {
        return new JsonResponse($data, $status, $headers);
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

    /**
     * @param array<string, string> $headers
     */
    public function redirectUrl(
        string $url,
        int $status = 302,
        array $headers = [],
    ): RedirectResponse {
        return new RedirectResponse($url, $status, $headers);
    }

    /**
     * @param array<string,int|string> $params
     * @param array<string, string> $headers
     */
    public function redirect(
        string $route,
        array $params = [],
        int $status = 302,
        array $headers = [],
    ): RedirectResponse {
        $url = $this->urlGenerator->generate($route, $params);
        return new RedirectResponse($url, $status, $headers);
    }
}
