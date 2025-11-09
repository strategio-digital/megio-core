<?php
declare(strict_types=1);

namespace Megio\Translation\Extension;

use Megio\Extension\Latte\Engine;
use Megio\Translation\Translator;
use Nette\DI\CompilerExtension;

class TranslationExtension extends CompilerExtension
{
    public function beforeCompile(): void
    {
        $builder = $this->getContainerBuilder();

        // Get Latte Engine and Translator services
        $latte = $builder->getDefinitionByType(Engine::class);
        $translator = $builder->getDefinitionByType(Translator::class);

        // Register TranslatorExtension to Latte
        $this->initialization->addBody(
            '$this->getService(?)->addExtension(new \Latte\Essential\TranslatorExtension($this->getService(?)->translate(...)));',
            [
                $latte->getName(),
                $translator->getName(),
            ],
        );

        // Set Translator to auto-inject into all templates
        $this->initialization->addBody(
            '$this->getService(?)->setTranslator($this->getService(?));',
            [
                $latte->getName(),
                $translator->getName(),
            ],
        );
    }
}
