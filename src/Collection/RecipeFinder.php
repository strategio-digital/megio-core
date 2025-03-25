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
        
        $path = Path::appDir();
        foreach (Finder::findFiles()->from($path) as $file) {
            $className = $this->getClassname($file, $path, 'App\\');
            if (is_subclass_of($className, ICollectionRecipe::class)) {
                /** @var ICollectionRecipe|null $recipe */
                $recipe = $this->container->getByType($className, false);
                if (!$recipe) {
                    /** @var ICollectionRecipe $recipe */
                    $recipe = $this->container->createInstance($className);
                }
                $this->recipes[] = $recipe;
            }
        }
        
        $path = Path::megioVendorDir() . '/src/Recipe';
        foreach (Finder::findFiles()->from($path) as $file) {
            $className = $this->getClassname($file, $path, 'Megio\\Recipe\\');
            
            if (is_subclass_of($className, ICollectionRecipe::class)) {
                /** @var ICollectionRecipe|null $recipe */
                $recipe = $this->container->getByType($className, false);
                if (!$recipe) {
                    /** @var ICollectionRecipe $recipe */
                    $recipe = $this->container->createInstance($className);
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
    
    private function getClassname(\SplFileInfo $file, string $path, string $namespacePrefix): string
    {
        $namespace = str_replace(realpath($path) . '/', '', $file->getRealPath());
        $namespace = str_replace('/', '\\', $namespace);
        
        return $namespacePrefix . str_replace('.php', '', $namespace);
    }
}