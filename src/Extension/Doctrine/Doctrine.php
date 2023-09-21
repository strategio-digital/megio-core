<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Extension\Doctrine;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Nette\Utils\FileSystem;
use Saas\Extension\Doctrine\Subscriber\PostgresDefaultSchemaSubscriber;
use Saas\Extension\Doctrine\Subscriber\SqliteForeignKeyChecksSubscriber;
use Saas\Helper\Path;
use Doctrine\DBAL\Configuration;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

class Doctrine
{
    protected EntityManager $entityManager;
    
    protected Connection $connection;
    
    protected Configuration $configuration;
    
    /** @var array<string, string> */
    protected array $connectionConfig = [];
    
    public function __construct()
    {
        $srcEntityPath = Path::appDir() . '/Database/Entity';
        $entityPaths = array_merge([Path::saasVendorDir() . '/src/Database/Entity'], file_exists($srcEntityPath) ? [$srcEntityPath] : []);
        
        $this->configuration = ORMSetup::createAttributeMetadataConfiguration(
            $entityPaths,
            $_ENV['APP_ENV_MODE'] === 'develop',
            Path::tempDir() . '/doctrine/proxy',
            new PhpFilesAdapter('dp')
        );
        
        $this->configuration->setMetadataCache(new PhpFilesAdapter('meta', 0, Path::tempDir() . '/doctrine/metadata'));
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
        
        $this->connection = DriverManager::getConnection($this->connectionConfig, $this->configuration, $evm);
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
}