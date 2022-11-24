<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Request\User;

use Saas\Http\Request\Auth;
use Saas\Http\Request\IRequest;
use Saas\Http\Response\Response;
use Saas\Storage\Storage;
use Nette\Schema\Expect;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadAvatarRequest implements IRequest
{
    public function __construct(
        private readonly Response $response,
        private readonly Auth     $auth,
        private readonly Storage  $storage
    )
    {
    }
    
    public function schema(): array
    {
        return [
            'avatar' => Expect::type(UploadedFile::class)->required()
        ];
    }
    
    /**
     * @param array{avatar: UploadedFile} $data
     * @return void
     * @throws \Exception
     */
    public function process(array $data): void
    {
        $user = $this->auth->getUser();
        
        $this->storage->get()->deleteFolder("user/{$user->getId()}/avatar/");
        $file = $this->storage->get()->upload($data['avatar'], "user/{$user->getId()}/avatar/");
        
        $this->response->send(['message' => "File '{$data['avatar']->getClientOriginalName()}' successfully uploaded to '{$file->getPathname()}'"]);
    }
}