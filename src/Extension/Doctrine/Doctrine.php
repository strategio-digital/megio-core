<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Extension\Doctrine;

use Nette\Utils\FileSystem;
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
    
    protected Configuration $configuration;
    
    /** @var array<string, string> */
    protected array $connectionConfig = [];
    
    public function __construct()
    {
        $srcEntityPath = Path::srcDir() . '/Database/Entity';
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
            'host' => $_ENV['DB_HOST'],
            'port' => $_ENV['DB_PORT'],
            'user' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
            'dbname' => $_ENV['DB_DATABASE']
        ];
        
        if ($_ENV['DB_DRIVER'] === 'pdo_sqlite' || $_ENV['DB_DRIVER'] === 'sqlite3') {
            $this->connectionConfig['path'] = $_ENV['DB_SQLITE_FILE'];
            if (!file_exists($_ENV['DB_SQLITE_FILE'])) {
                FileSystem::write($_ENV['DB_SQLITE_FILE'], '');
            }
        }
    
        if (!file_exists(Path::srcDir() . '/../migrations')) {
            FileSystem::createDir(Path::srcDir() . '/../migrations');
        }
        
        $this->entityManager = EntityManager::create($this->connectionConfig, $this->configuration);
    }
    
    public function getMigrationFactory(): DependencyFactory
    {
        $conf = new ConfigurationArray([
            'table_storage' => [
                'table_name' => 'fw_migrations',
                'version_column_name' => 'version',
                'version_column_length' => 1024,
                'executed_at_column_name' => 'executed_at',
                'execution_time_column_name' => 'execution_time',
            ],
            
            'migrations_paths' => [
                'App\\Migrations' => Path::srcDir() . '/../migrations',
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
}