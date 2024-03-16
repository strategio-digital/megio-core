<?php
declare(strict_types=1);

namespace Megio\Collection;

use Megio\Helper\Path;
use Nette\Utils\Finder;

class RecipeFinder
{
    /** @var ICollectionRecipe[] */
    protected array $recipes = [];
    
    public function load(): self
    {
        $this->recipes = [];
        
        $appFiles = Finder::findFiles()->from(Path::appDir() . '/Recipe');
        foreach ($appFiles as $file) {
            $class = 'App\\Recipe\\' . $file->getBasename('.php');
            if (is_subclass_of($class, ICollectionRecipe::class)) {
                $this->recipes[] = new $class();
            }
        }
        
        $vendorFiles = Finder::findFiles()->from(Path::megioVendorDir() . '/src/Recipe');
        foreach ($vendorFiles as $file) {
            $class = 'Megio\\Recipe\\' . $file->getBasename('.php');
            if (is_subclass_of($class, ICollectionRecipe::class)) {
                $this->recipes[] = new $class();
            }
        }
        
        return $this;
    }
    
    /** @return ICollectionRecipe[] */
    public function getAll(): array
    {
        return $this->recipes;
    }
    
    public function findByName(string $name): ?ICollectionRecipe
    {
        $recipe = current(array_filter($this->recipes, fn($r) => $r->name() === $name));
        return $recipe ?: null;
    }
}