<?php
declare(strict_types=1);

namespace Megio\Extension;

use Nette;
use Nette\DI\CompilerExtension;

class Extension extends CompilerExtension
{
    public function getConfigSchema(): Nette\Schema\Schema
    {
        return Nette\Schema\Expect::arrayOf('string', 'string');
    }
}