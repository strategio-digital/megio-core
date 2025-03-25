<?php

namespace Tests;

use App\EntityManager;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Megio\Bootstrap;
use Megio\Collection\CollectionRecipe;
use Megio\Debugger\JsonLogstashLogger;
use Megio\Helper\Path;
use Megio\Http\Request\Request as MegioRequest;
use Nette\DI\Container;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\HttpFoundation\Request;

abstract class MegioTestCase extends BaseTestCase
{
    private static ?Container $container = null;
    
    private static ?Generator $generator = null;
    
    public function em(): EntityManager
    {
        return $this->container()->getByType(EntityManager::class);
    }
    
    public function generator(): Generator
    {
        if (self::$generator === null) {
            self::$generator = FakerFactory::create();
        }
        
        return self::$generator;
    }
    
    public function httpRequest(): Request
    {
        return Request::createFromGlobals();
    }
    
    public function container(): Container
    {
        if (self::$container === null) {
            $bootstrap = (new Bootstrap())
                ->projectRootPath(__DIR__ . '/../')
                ->logger(new JsonLogstashLogger());
            
            self::$container = $bootstrap->configure(
                configPath: Path::configDir() . '/tests.neon',
                startedAt: microtime(true)
            );
        }
        
        return self::$container;
    }
    
    /**
     * @param class-string<MegioRequest> $class
     * @return MegioRequest
     */
    public function createCollectionRequest(string $class): MegioRequest
    {
        $request = $this->container()->createInstance($class);
        assert($request instanceof MegioRequest);
        $request->__inject($this->container());
        $request->__invoke($this->httpRequest());
        
        return $request;
    }
    
    /**
     * @param class-string<CollectionRecipe> $class
     * @return CollectionRecipe
     */
    public function createCollectionRecipe(string $class): CollectionRecipe
    {
        $recipe = $this->container()->createInstance($class);
        assert($recipe instanceof CollectionRecipe);
        return $recipe;
    }
}
