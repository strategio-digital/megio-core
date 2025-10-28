<?php
declare(strict_types=1);

namespace Megio\Database\Interface;

use DateTime;

interface ICrudable
{
    public function getId(): string;

    public function getCreatedAt(): DateTime;

    public function getUpdatedAt(): DateTime;
}
