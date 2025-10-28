<?php
declare(strict_types=1);

namespace Megio\Database\Interface;

interface IJoinable
{
    public function getId(): string;

    /**
     * @return array{fields: string[], format: string}
     */
    public function getJoinableLabel(): array;
}
