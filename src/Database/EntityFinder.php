<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Database;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Table;
use Megio\Database\Entity\Auth\Resource;
use Megio\Database\Entity\Auth\Role;
use Megio\Database\Entity\Auth\Token;

class EntityFinder
{
    const EXCLUDED_EVERYWHERE = [Role::class, Resource::class, Token::class];
    
    public function __construct(protected EntityManager $em)
    {
    }
    
    /**
     * @return array<int, array{table: string, className: class-string}>
     */
    public function findAll(): array
    {
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        
        $entities = array_map(function (ClassMetadata $metadata) {
            $attr = $metadata->getReflectionClass()->getAttributes(Table::class)[0]->newInstance();
            return [
                'table' => str_replace('`', '', $attr->name ?? ''),
                'className' => $metadata->name
            ];
        }, $metadata);
        
        return array_filter($entities, fn($entity) => !in_array($entity['className'], self::EXCLUDED_EVERYWHERE));
    }
    
    
    /**
     * @param string $tableName
     * @return class-string|null
     */
    public function getClassName(string $tableName): ?string
    {
        $entityNames = $this->findAll();
        $current = current(array_filter($entityNames, fn($namespace) => $namespace['table'] === $tableName));
        return $current ? $current['className'] : null;
    }
}