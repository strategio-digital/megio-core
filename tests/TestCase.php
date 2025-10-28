<?php declare(strict_types=1);

namespace Tests;

use App\EntityManager;
use Exception;
use Megio\Collection\CollectionRecipe;
use Megio\Debugger\JsonLogstashLogger;
use Megio\Helper\Path;
use Megio\Http\Request\Request as MegioRequest;
use Nette\DI\Container;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\HttpFoundation\Request;
use Tests\Bootstrap as TestBootstrap;

abstract class TestCase extends BaseTestCase
{
    protected EntityManager $em;

    private Container $container;

    /**
     * @template T of MegioRequest
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    public function createCollectionRequest(string $class): object
    {
        $request = $this->container->createInstance($class);
        assert($request instanceof MegioRequest);

        /** @var T $request */
        $request->__inject($this->container);
        $request->__invoke(Request::createFromGlobals());

        return $request;
    }

    /**
     * @template T of CollectionRecipe
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    public function createCollectionRecipe(string $class): object
    {
        $recipe = $this->container->createInstance($class);
        assert($recipe instanceof CollectionRecipe);

        /** @var T $recipe */
        return $recipe;
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->container = $this->createContainer();
        $this->em = $this->container->getByType(EntityManager::class);

        // Clear database state before each test
        $this->em->beginTransaction();
    }

    protected function tearDown(): void
    {
        // Rollback transaction to keep tests isolated
        if ($this->em->getConnection()->isTransactionActive()) {
            $this->em->rollback();
        }

        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    private function createContainer(): Container
    {
        return new TestBootstrap()
            ->projectRootPath(__DIR__ . '/../')
            ->logger(new JsonLogstashLogger())
            ->configure(
                configPath: Path::configDir() . '/tests.neon',
                startedAt: microtime(true),
            );
    }
}
