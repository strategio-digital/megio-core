<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Database\Interface;

interface AuthUser
{
    public function getId(): string;
    
    public function getPassword(): string;
    
    public function getEmail(): string;
    
    public function setLastLogin(): AuthUser;
}