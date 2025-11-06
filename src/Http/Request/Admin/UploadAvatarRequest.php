<?php
declare(strict_types=1);

namespace Megio\Http\Request\Admin;

use Exception;
use Megio\Http\Request\AbstractRequest;
use Megio\Security\Auth\AuthUser;
use Megio\Storage\Storage;
use Nette\Schema\Expect;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UploadAvatarRequest extends AbstractRequest
{
    public function __construct(
        protected readonly AuthUser $user,
        protected readonly Storage $storage,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function schema(): array
    {
        return ['avatar' => Expect::type(UploadedFile::class)->required()];
    }

    /**
     * @param array{avatar: UploadedFile} $data
     *
     * @throws Exception
     */
    public function processValidatedData(array $data): Response
    {
        $user = $this->user->get();

        if (!$user) {
            return $this->error(['errors' => ['You are not logged in']]);
        }

        $this->storage->get()->deleteFolder("user/{$user->getId()}/avatar/");
        $file = $this->storage->get()->upload($data['avatar'], "user/{$user->getId()}/avatar/");

        return $this->json([
            'message' => "File successfully uploaded.'",
            'name' => $file->getFilename(),
            'destination' => $file->getPathname(),
        ]);
    }

    public function process(Request $request): Response
    {
        return new Response('ProcessValidatedData() is deprecated', 500);
    }
}
