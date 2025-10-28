<?php
declare(strict_types=1);

namespace Megio\Extension\Doctrine;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use Megio\Extension\Doctrine\Middleware\LoggingMiddleware;
use Megio\Extension\Doctrine\Middleware\QueryLogger;
use Megio\Helper\Path;
use Nette\Utils\FileSystem;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use Symfony\Component\Cache\Exception\CacheException;

use const CASE_LOWER;

class Doctrine
{
    public const string MIGRATION_TABLE = '_migration_versions';

    public EntityManager $entityManager {
        get {
            return $this->entityManager;
        }
    }

    protected Connection $connection {
        get {
            return $this->connection;
        }
    }

    protected QueryLogger $queryLogger {
        get {
            return $this->queryLogger;
        }
    }

    protected Configuration $configuration;

    /** @var array<string, string> */
    protected array $connectionConfig = [];

    /**
     * @throws CacheException
     */
    public function __construct(QueryLogger $queryLogger)
    {
        $this->queryLogger = $queryLogger;

        $srcEntityPath = Path::appDir();

        $entityPaths = array_merge(
            [Path::megioVendorDir() . '/src/Database/Entity'],
            file_exists($srcEntityPath) ? [$srcEntityPath] : [],
        );

        $proxyAdapter
            = $_ENV['APP_ENVIRONMENT'] === 'develop'
                ? new ArrayAdapter()
                : new PhpFilesAdapter('dp', 0, Path::tempDir() . '/doctrine/proxy');

        $metadataAdapter
            = $_ENV['APP_ENVIRONMENT'] === 'develop'
                ? new ArrayAdapter()
                : new PhpFilesAdapter('meta', 0, Path::tempDir() . '/doctrine/metadata');

        $this->configuration = ORMSetup::createAttributeMetadataConfiguration(
            paths: $entityPaths,
            isDevMode: $_ENV['APP_ENVIRONMENT'] === 'develop',
            proxyDir: Path::tempDir() . '/doctrine/proxy',
            cache: $proxyAdapter,
        );

        $this->configuration->setMetadataCache($metadataAdapter);
        $this->configuration->setNamingStrategy(new UnderscoreNamingStrategy(CASE_LOWER));

        $this->connectionConfig = [
            'driver' => $_ENV['DB_DRIVER'],
            'charset' => 'UTF8',
        ];

        if ($_ENV['DB_DRIVER'] === 'pdo_pgsql' || $_ENV['DB_DRIVER'] === 'pdo_mysql') {
            $this->connectionConfig['host'] = $_ENV['DB_HOST'];
            $this->connectionConfig['port'] = $_ENV['DB_PORT'];
            $this->connectionConfig['dbname'] = $_ENV['DB_DATABASE'];
            $this->connectionConfig['user'] = $_ENV['DB_USERNAME'];
            $this->connectionConfig['password'] = $_ENV['DB_PASSWORD'];
        }

        if (!file_exists(Path::appDir() . '/../migrations')) {
            FileSystem::createDir(Path::appDir() . '/../migrations');
        }


        $evm = new EventManager();
        $dbalConfig = new Configuration();
        $dbalConfig->setMiddlewares([new LoggingMiddleware($this->queryLogger)]);

        // Remove migrations table from default schema management
        $dbalConfig->setSchemaAssetsFilter(static fn(string $assetName): bool => $assetName !== self::MIGRATION_TABLE);

        $this->connection = DriverManager::getConnection($this->connectionConfig, $dbalConfig);
        $this->entityManager = new EntityManager($this->connection, $this->configuration, $evm);
    }

    public function getMigrationFactory(): DependencyFactory
    {
        $conf = new ConfigurationArray([
            'table_storage' => [
                'table_name' => self::MIGRATION_TABLE,
                'version_column_name' => 'version',
                'version_column_length' => 1024,
                'executed_at_column_name' => 'executed_at',
                'execution_time_column_name' => 'execution_time',
            ],

            'migrations_paths' => [
                'App\\Migrations' => Path::appDir() . '/../migrations',
            ],

            'all_or_nothing' => true,
            'transactional' => true,
            'check_database_platform' => true,
            'organize_migrations' => 'none',
            'connection' => null,
            'em' => null,
        ]);

        return DependencyFactory::fromEntityManager($conf, new ExistingEntityManager($this->entityManager));
    }
}
