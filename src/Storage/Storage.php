<?php
declare(strict_types=1);

namespace Megio\Storage;

use Nette\DI\Container;
use Nette\Utils\Strings;

class Storage
{
    public function __construct(private readonly Container $container)
    {
    }
    
    /**
     * @param string|null $adapterName
     * @return StorageAdapter
     * @throws \Exception
     */
    public function get(string $adapterName = null): StorageAdapter
    {
        $className = $this->getAdapterClass($adapterName);
        
        /** @var StorageAdapter $instance */
        $instance = $this->container->getByType($className, false) ?: $this->container->createInstance($className);
        return $instance;
    }
    
    /**
     * @param string|null $type
     * @return class-string
     * @throws \Exception
     */
    public function getAdapterClass(string $type = null): string
    {
        $type = $type ?: $this->getAdapterName();
        $type = Strings::firstUpper($type);
        $className = "Megio\\Storage\\Adapter\\{$type}Storage";
        
        if (!class_exists($className)) {
            throw new \Exception("Storage adapter class '{$className}' does not exists");
        }
        
        return $className;
    }
    
    /**
     * @return string
     * @throws \Exception
     */
    public function getAdapterName(): string
    {
        $className = $this->getAdapterClass($_ENV['APP_STORAGE_ADAPTER']);
        
        if (!class_exists($className)) {
            throw new \Exception("Storage adapter class '{$className}' does not exists");
        }
        
        return $_ENV['APP_STORAGE_ADAPTER'];
    }
}