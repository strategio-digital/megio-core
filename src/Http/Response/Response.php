<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Response;

use Latte\Engine;
use Saas\Debugger\Debugger;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\Routing\Generator\UrlGenerator;

class Response
{
    public function __construct(
        private readonly Debugger $debugger,
        protected SymfonyResponse $response,
        protected UrlGenerator    $urlGenerator,
        protected Engine          $latte
    )
    {
    }
    
    public function getHttpResponse(): SymfonyResponse
    {
        return $this->response;
    }
    
    public function sendRawResponse(): never
    {
        $this->response->sendHeaders();
        $this->response->sendContent();
        
        if ($_ENV['APP_ENV_MODE'] !== 'develop') {
            fastcgi_finish_request();
        }
        exit;
    }
    
    public function sendRawHeaders(): never
    {
        $this->response->sendHeaders();
        
        if ($_ENV['APP_ENV_MODE'] !== 'develop') {
            fastcgi_finish_request();
        }
        exit;
    }
    
    /**
     * @param array<string|int,mixed> $data
     * @param int $code
     * @return never
     */
    public function send(array $data = [], int $code = 200): never
    {
        $data = $this->debugger->formatResponseData($data);
        exit((new JsonResponse($data, $code))->send());
    }
    
    /**
     * @param array<string|int,mixed> $messages
     * @param int $code
     * @return never
     */
    public function sendError(array $messages, int $code = 400): never
    {
        $data = ['errors' => $messages];
        $data = $this->debugger->formatResponseData($data);
        
        exit((new JsonResponse($data, $code))->send());
    }
    
    /**
     * @param File $file
     * @return never
     */
    public function sendFile(File $file): never
    {
        $this->response->setContent($file->getContent());
        $disposition = HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $file->getFilename());
        
        $this->response->headers->set('Content-Disposition', $disposition);
        $this->sendRawResponse();
    }
    
    public function sendFileContent(string $content, string $fileName): never
    {
        $this->response->setContent($content);
        
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $fileName
        );
        
        $this->response->headers->set('Content-Disposition', $disposition);
        $this->sendRawResponse();
    }
    
    /**
     * @param string $path
     * @param array<string, mixed> $params
     * @return never
     */
    public function render(string $path, array $params = []): never
    {
        $html = $this->latte->renderToString($path, $params);
        $this->getHttpResponse()->headers->set('Content-Type', 'text/html');
        $this->getHttpResponse()->setContent($html);
        $this->sendRawResponse();
    }
}