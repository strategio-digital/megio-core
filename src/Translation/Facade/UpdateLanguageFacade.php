<?php

declare(strict_types=1);

namespace Megio\Translation\Facade;

use Doctrine\ORM\Exception\ORMException;
use Megio\Database\Entity\Translation\Language;
use Megio\Database\EntityManager;
use Megio\Translation\Facade\Exception\LanguageFacadeException;
use Megio\Translation\Http\Request\Dto\LanguageUpdateDto;

final readonly class UpdateLanguageFacade
{
    public function __construct(
        private EntityManager $em,
    ) {}

    /**
     * @throws LanguageFacadeException
     * @throws ORMException
     */
    public function execute(LanguageUpdateDto $dto): Language
    {
        $language = $this->em->getLanguageRepo()->find($dto->id);

        if ($language === null) {
            throw new LanguageFacadeException('Language not found');
        }

        if ($dto->isDefault === true) {
            $this->em->getLanguageRepo()->unsetAllDefaults();
        }

        $language->setName($dto->name);
        $language->setIsDefault($dto->isDefault);
        $language->setIsEnabled($dto->isEnabled);

        $this->em->flush();

        return $language;
    }
}
