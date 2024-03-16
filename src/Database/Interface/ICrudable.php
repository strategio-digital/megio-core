<?php
declare(strict_types=1);

namespace Megio\Database\Interface;

interface ICrudable
{
    public function getId(): string;
    
    public function getCreatedAt(): \DateTime;
    
    public function getUpdatedAt(): \DateTime;
}