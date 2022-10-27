<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Framework\Http\Response;

use Framework\Debugger\Debugger;
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
        protected UrlGenerator    $urlGenerator
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
     * @param array<string,mixed> $messages
     * @param int $code
     * @return never
     */
    public function sendError(array $messages, int $code = 400): never
    {
        $messages = $this->debugger->formatResponseData($messages);
        exit((new JsonResponse($messages, $code))->send());
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
}