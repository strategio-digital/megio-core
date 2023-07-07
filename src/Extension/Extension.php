<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Extension;

use Nette;
use Nette\DI\CompilerExtension;

class Extension extends CompilerExtension
{
    public function getConfigSchema(): Nette\Schema\Schema
    {
        return Nette\Schema\Expect::arrayOf('string', 'string');
    }
}