<?php
declare(strict_types=1);

namespace Megio\Http\Request\Admin;

use Exception;
use Megio\Http\Request\Request;
use Megio\Security\Auth\AuthUser;
use Megio\Storage\Storage;
use Nette\Schema\Expect;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class UploadAvatarRequest extends Request
{
    public function __construct(protected readonly AuthUser $user, protected readonly Storage $storage) {}

    public function schema(array $data): array
    {
        return ['avatar' => Expect::type(UploadedFile::class)->required()];
    }

    /**
     * @param array{avatar: UploadedFile} $data
     *
     * @throws Exception
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
            'destination' => $file->getPathname(),
        ]);
    }
}
