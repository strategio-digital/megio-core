<?php

declare(strict_types=1);

namespace Megio\Translation\Http\Request;

use Doctrine\ORM\Exception\ORMException;
use Megio\Http\Request\AbstractRequest;
use Megio\Http\Serializer\RequestSerializerException;
use Megio\Translation\Facade\Exception\LanguageFacadeException;
use Megio\Translation\Facade\UpdateLanguageFacade;
use Megio\Translation\Http\Request\Dto\LanguageUpdateDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LanguageUpdateRequest extends AbstractRequest
{
    public function __construct(
        private readonly UpdateLanguageFacade $updateLanguageFacade,
    ) {}

    /**
     * @throws ORMException
     * @throws RequestSerializerException
     */
    public function process(Request $request): Response
    {
        $dto = $this->requestToDto(LanguageUpdateDto::class);

        try {
            $language = $this->updateLanguageFacade->execute($dto);
        } catch (LanguageFacadeException $e) {
            return $this->error(['general' => $e->getMessage()]);
        }

        return $this->json([
            'id' => $language->getId(),
            'posix' => $language->getPosix(),
            'shortCode' => $language->getShortCode(),
            'name' => $language->getName(),
            'isDefault' => $language->isDefault(),
            'isEnabled' => $language->isEnabled(),
        ]);
    }
}
