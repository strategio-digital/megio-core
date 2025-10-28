<?php
declare(strict_types=1);

namespace Megio\Database\Field;

use Doctrine\ORM\Mapping as ORM;
use Megio\Database\Entity\EntityException;
use Nette\Utils\Validators;

trait TEmail
{
    #[ORM\Column(length: 64, unique: true, nullable: false)]
    protected string $email;

    /**
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @throws EntityException
     */
    public function setEmail(string $email): self
    {
        if (!Validators::isEmail($email)) {
            throw new EntityException('E-mail address is not valid');
        }

        $this->email = $email;
        return $this;
    }
}
