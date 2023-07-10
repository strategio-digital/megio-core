<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Database\Interface;

interface ICrudable
{
    public function getId(): string;
    
    public function getCreatedAt(): \DateTime;
    
    public function getUpdatedAt(): \DateTime;
}