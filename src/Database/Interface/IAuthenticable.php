<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Database\Interface;

use Doctrine\Common\Collections\Collection;
use Saas\Database\Entity\Auth\Role;

interface IAuthenticable
{
    public function getId(): string;
    
    public function getPassword(): string;
    
    public function getEmail(): string;
    
    /** @return Collection<int, Role> */
    public function getRoles(): Collection;
    
    public function setLastLogin(): IAuthenticable;
    
    public function setPassword(string $password): IAuthenticable;
}