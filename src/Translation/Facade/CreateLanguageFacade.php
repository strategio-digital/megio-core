<?php

declare(strict_types=1);

namespace Megio\Translation\Facade;

use Doctrine\ORM\Exception\ORMException;
use Megio\Database\Entity\Translation\Language;
use Megio\Database\EntityManager;
use Megio\Translation\Facade\Exception\LanguageFacadeException;
use Megio\Translation\Helper\PosixHelper;
use Megio\Translation\Http\Request\Dto\LanguageCreateDto;

final readonly class CreateLanguageFacade
{
    public function __construct(
        private EntityManager $em,
    ) {}

    /**
     * @throws LanguageFacadeException
     * @throws ORMException
     */
    public function execute(LanguageCreateDto $dto): Language
    {
        $exists = $this->em->getLanguageRepo()->findOneByPosix($dto->posix);

        if ($exists !== null) {
            throw new LanguageFacadeException('Language with this POSIX already exists');
        }

        // If setting as default, unset other defaults
        if ($dto->isDefault === true) {
            $this->em->getLanguageRepo()->unsetAllDefaults();
        }

        $language = new Language();
        $language->setPosix($dto->posix);
        $language->setShortCode(PosixHelper::extractShortCode($dto->posix));
        $language->setName($dto->name);
        $language->setIsDefault($dto->isDefault);
        $language->setIsEnabled($dto->isEnabled);

        $this->em->persist($language);
        $this->em->flush();

        return $language;
    }
}
