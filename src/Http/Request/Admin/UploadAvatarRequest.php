<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Request\Admin;

use Saas\Http\Request\Request;
use Saas\Security\Auth\AuthUser;
use Saas\Storage\Storage;
use Nette\Schema\Expect;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class UploadAvatarRequest extends Request
{
    public function __construct(protected readonly AuthUser $user, protected readonly Storage $storage)
    {
    }
    
    public function schema(): array
    {
        return ['avatar' => Expect::type(UploadedFile::class)->required()];
    }
    
    /**
     * @param array{avatar: UploadedFile} $data
     * @return Response
     * @throws \Exception
     */
    public function process(array $data): Response
    {
        $user = $this->user->get();
        
        if (!$user) {
            return $this->error(['You are not logged in']);
        }
        
        $this->storage->get()->deleteFolder("user/{$user->getId()}/avatar/");
        $file = $this->storage->get()->upload($data['avatar'], "user/{$user->getId()}/avatar/");
        
        return $this->json([
            'message' => "File successfully uploaded.'",
            'name' => $file->getFilename(),
            'destination' => $file->getPathname()
        ]);
    }
}