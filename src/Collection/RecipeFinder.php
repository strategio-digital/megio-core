<?php
declare(strict_types=1);

namespace Megio\Collection;

use Megio\Helper\Path;
use Nette\DI\Container;
use Nette\Utils\Finder;

class RecipeFinder
{
    /** @var ICollectionRecipe[] */
    protected array $recipes = [];
    
    public function __construct(protected Container $container)
    {
    }
    
    public function load(): self
    {
        $this->recipes = [];
        
        $appFiles = Finder::findFiles()->from(Path::appDir() . '/Recipe');
        foreach ($appFiles as $file) {
            $class = 'App\\Recipe\\' . $file->getBasename('.php');
            if (is_subclass_of($class, ICollectionRecipe::class)) {
                /** @var ICollectionRecipe|null $recipe */
                $recipe = $this->container->getByType($class, false);
                if (!$recipe) {
                    /** @var ICollectionRecipe $recipe */
                    $recipe = $this->container->createInstance($class);
                }
                $this->recipes[] = $recipe;
            }
        }
        
        $vendorFiles = Finder::findFiles()->from(Path::megioVendorDir() . '/src/Recipe');
        foreach ($vendorFiles as $file) {
            $class = 'Megio\\Recipe\\' . $file->getBasename('.php');
            if (is_subclass_of($class, ICollectionRecipe::class)) {
                /** @var ICollectionRecipe|null $recipe */
                $recipe = $this->container->getByType($class, false);
                if (!$recipe) {
                    /** @var ICollectionRecipe $recipe */
                    $recipe = $this->container->createInstance($class);
                }
                $this->recipes[] = $recipe;
            }
        }
        return $this;
    }
    
    /** @return ICollectionRecipe[] */
    public function getAll(): array
    {
        return $this->recipes;
    }
    
    public function findByKey(string $key): ?ICollectionRecipe
    {
        $recipe = current(array_filter($this->recipes, fn($r) => $r->key() === $key));
        return $recipe ?: null;
    }
}