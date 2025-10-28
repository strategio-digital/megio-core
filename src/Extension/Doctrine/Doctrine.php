<?php
declare(strict_types=1);

namespace Megio\Extension\Doctrine;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Nette\Utils\FileSystem;
use Megio\Extension\Doctrine\Subscriber\PostgresDefaultSchemaSubscriber;
use Megio\Extension\Doctrine\Subscriber\SqliteForeignKeyChecksSubscriber;
use Megio\Helper\Path;
use Doctrine\DBAL\Configuration;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use Megio\Extension\Doctrine\Middleware\QueryLogger;
use Megio\Extension\Doctrine\Middleware\LoggingMiddleware;

class Doctrine
{
    protected EntityManager $entityManager;

    protected Connection $connection;

    protected Configuration $configuration;

    /** @var array<string, string> */
    protected array $connectionConfig = [];

    protected QueryLogger $queryLogger;

    public function __construct(QueryLogger $queryLogger)
    {
        $this->queryLogger = $queryLogger;

        $srcEntityPath = Path::appDir();

        $entityPaths = array_merge(
            [Path::megioVendorDir() . '/src/Database/Entity'],
            file_exists($srcEntityPath) ? [$srcEntityPath] : [],
        );

        $proxyAdapter =
            $_ENV['APP_ENVIRONMENT'] === 'develop'
                ? new \Symfony\Component\Cache\Adapter\ArrayAdapter()
                : new PhpFilesAdapter('dp', 0, Path::tempDir() . '/doctrine/proxy');

        $metadataAdapter =
            $_ENV['APP_ENVIRONMENT'] === 'develop'
                ? new \Symfony\Component\Cache\Adapter\ArrayAdapter()
                : new PhpFilesAdapter('meta', 0, Path::tempDir() . '/doctrine/metadata');

        $this->configuration = ORMSetup::createAttributeMetadataConfiguration(
            paths: $entityPaths,
            isDevMode: $_ENV['APP_ENVIRONMENT'] === 'develop',
            proxyDir: Path::tempDir() . '/doctrine/proxy',
            cache: new PhpFilesAdapter('dp'),
        );

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

        if ($_ENV['DB_DRIVER'] === 'pdo_sqlite') {
            $filePath = $_ENV['DB_SQLITE_FILE'];

            if (!FileSystem::isAbsolute($filePath)) {
                $filePath = Path::appDir() . '/../docker/temp/sqlite/' . $filePath;
            }

            if (!file_exists($filePath)) {
                FileSystem::write($filePath, '');
            }

            $this->connectionConfig['path'] = $filePath;
        }

        if (!file_exists(Path::appDir() . '/../migrations')) {
            FileSystem::createDir(Path::appDir() . '/../migrations');
        }

        $evm = new EventManager();
        $evm->addEventSubscriber(new PostgresDefaultSchemaSubscriber());
        $evm->addEventSubscriber(new SqliteForeignKeyChecksSubscriber());

        // Add logging middleware and event manager to DBAL configuration
        $dbalConfig = new \Doctrine\DBAL\Configuration();
        $dbalConfig->setMiddlewares([new LoggingMiddleware($this->queryLogger)]);

        $this->connection = DriverManager::getConnection($this->connectionConfig, $dbalConfig);
        $this->entityManager = new EntityManager($this->connection, $this->configuration, $evm);
    }

    public function getMigrationFactory(): DependencyFactory
    {
        $conf = new ConfigurationArray([
            'table_storage' => [
                'table_name' => 'migration_versions',
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
            'em' => null
        ]);

        return DependencyFactory::fromEntityManager($conf, new ExistingEntityManager($this->entityManager));
    }

    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function getQueryLogger(): QueryLogger
    {
        return $this->queryLogger;
    }
}