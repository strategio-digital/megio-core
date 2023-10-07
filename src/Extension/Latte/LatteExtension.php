<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Extension\Latte;

use Latte\Engine;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

class LatteExtension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'extensions' => Expect::arrayOf('string')->required(),
        ]);
    }
    
    public function loadConfiguration(): void
    {
        /** @var \stdClass $config */
        $config = $this->getConfig();
        $builder = $this->getContainerBuilder();
        
        // Setup latte engine
        $d = $builder->addDefinition('latte')->setType(Engine::class);
        $this->initialization->addBody('$latte = $this->getService(?);', [$d->getName()]);
        $this->initialization->addBody('$latte->setAutoRefresh($_ENV["APP_ENV_MODE"] === "develop");');
        $this->initialization->addBody('$latte->setTempDirectory(Megio\Helper\Path::tempDir() . "/latte");');
        
        // Register latte extensions
        foreach ($config->extensions as $key => $className) {
            $d = $builder->addDefinition($this->prefix("latteExtension_$key"))->setType($className);
            $this->initialization->addBody('$latte->addExtension($this->getService(?));', [$d->getName()]);
        }
        
        // Add debugger panel
        $this->initialization->addBody('\Tracy\Debugger::getBar()->addPanel(new \Latte\Bridges\Tracy\LattePanel($latte));');
    }
}