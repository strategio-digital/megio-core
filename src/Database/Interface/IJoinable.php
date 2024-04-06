<?php
declare(strict_types=1);

namespace Megio\Database\Interface;

interface IJoinable
{
    public function getId(): string;
    
    public function getJoinableLabel(): string;
}