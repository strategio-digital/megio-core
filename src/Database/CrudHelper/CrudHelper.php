<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Database\CrudHelper;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Table;
use Saas\Database\Entity\Admin\Admin;
use Saas\Database\Entity\Role\Resource;
use Saas\Database\Entity\Role\Role;
use Saas\Database\Entity\User\Token;
use Saas\Database\EntityManager;
use Saas\Database\Interface\CrudEntity;

class CrudHelper
{
    /** @var class-string[] */
    const EXCLUDED_IN_CRUD = [Role::class, Resource::class, Token::class];
    
    /** @var class-string[] */
    const EXCLUDED_IN_COLLECTIONS = [Admin::class];
    
    protected ?string $error = null;
    
    public function __construct(protected EntityManager $em)
    {
    }
    
    public function getError(): ?string
    {
        return $this->error;
    }
    
    /**
     * @return array<int, array{table: string, value: class-string}>
     */
    public function getAllEntityClassNames(): array
    {
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        
        $entities = array_map(function (ClassMetadata $metadata) {
            $attr = $metadata->getReflectionClass()->getAttributes(Table::class)[0]->newInstance();
            return ['table' => str_replace('`', '', $attr->name ?? ''), 'value' => $metadata->name];
        }, $metadata);
        
        return array_filter($entities, fn($entity) => !in_array($entity['value'], self::EXCLUDED_IN_CRUD));
    }
    
    /**
     * @param string $tableName
     * @return class-string|null
     */
    public function getEntityClassName(string $tableName): ?string
    {
        $entityNames = $this->getAllEntityClassNames();
        $current = current(array_filter($entityNames, fn($namespace) => $namespace['table'] === $tableName));
        return $current ? $current['value'] : null;
    }
    
    /**
     * @param class-string $entityClassName
     * @return array<int, string>
     */
    public function getVisibleProps(string $entityClassName): array
    {
        try {
            $ref = new \ReflectionClass($entityClassName);
            $refProps = $ref->getProperty('visibleFields')->getDefaultValue();
            return array_merge(['id'], array_filter($refProps, fn($prop) => $prop !== 'id'));
        } catch (\ReflectionException) {
            return ['id'];
        }
    }
    
    /**
     * @param class-string $entityClassName
     * @return array<int, mixed>
     */
    public function getEntitySchema(string $entityClassName): array
    {
        $props = [];
        try {
            $ref = new \ReflectionClass($entityClassName);
            foreach ($ref->getProperties() as $prop) {
                $attrs = array_map(fn($attr) => $attr->newInstance(), $prop->getAttributes());
                
                /** @var Column[] $columnAttrs */
                $columnAttrs = array_filter($attrs, fn($attr) => $attr instanceof Column);
                if (count($columnAttrs) !== 0) {
                    $attr = array_values($columnAttrs)[0];
                    $props[] = $this->getEntityColumnProps($attr, $prop);
                }
            }
        } catch (\ReflectionException) {
        }
        
        // move array item with name "id" to first position
        $idProp = array_filter($props, fn($prop) => $prop['name'] !== 'id');
        return array_merge(array_values(array_filter($props, fn($prop) => $prop['name'] === 'id')), $idProp);
    }
    
    /**
     * @param string $tableName
     * @param bool $schema
     * @return \Saas\Database\CrudHelper\EntityMetadata|null
     */
    public function getEntityMetadata(string $tableName, bool $schema = false): ?EntityMetadata
    {
        $this->error = null;
        
        $className = $this->getEntityClassName($tableName);
        
        if (!$className) {
            $this->error = "Collection '{$tableName}' not found";
            return null;
        }
        
        $visibleProps = $this->getVisibleProps($className);
        
        //dumpe($visibleProps);
        
        if (count($visibleProps) === 1) { // 'id' is automatically included
            $this->error = "Collection '{$tableName}' has no visible fields";
            return null;
        }
        
        $fieldsSchema = [];
        
        if ($schema) {
            $fieldsSchema = $this->getEntitySchema($className);
        }
        
        return new EntityMetadata($className, $tableName, $visibleProps, $fieldsSchema);
    }
    
    /**
     * @param \Saas\Database\Interface\CrudEntity $entity
     * @param array<string, mixed> $props
     * @return \Saas\Database\Interface\CrudEntity
     * @throws \Saas\Database\CrudHelper\CrudException
     */
    public function setUpEntityProps(CrudEntity $entity, array $props): CrudEntity
    {
        $ref = new \ReflectionClass($entity);
        $methods = array_map(fn($method) => $method->name, $ref->getMethods());
        
        foreach ($props as $key => $value) {
            try {
                $methodName = 'set' . ucfirst($key);
                if (in_array($methodName, $methods)) {
                    $m = $ref->getMethod($methodName)->name;
                    $entity->$m($value);
                } else {
                    $ref->getProperty($key)->setValue($entity, $value);
                }
            } catch (\ReflectionException) {
                throw new CrudException("Property '{$key}' does not exist");
            }
            
        }
        
        return $entity;
    }
    
    /**
     * @param \Doctrine\ORM\Mapping\Column $attr
     * @param \ReflectionProperty $prop
     * @return array<string, mixed>
     */
    public function getEntityColumnProps(Column $attr, \ReflectionProperty $prop): array
    {
        $propType = $prop->getType();
        $nullable = $attr->nullable;
        
        $type = $attr->type;
        if ($type === null) {
            $type = $propType instanceof \ReflectionNamedType ? $propType->getName() : $propType ?? '@unknown';
        }
        
        $maxLength = $attr->length;
        if ($maxLength === null && $type === 'string') {
            $maxLength = 255;
        }
        
        return [
            'name' => $prop->getName(),
            'type' => mb_strtolower($type),
            'nullable' => $nullable,
            'maxLength' => $maxLength
        ];
    }
}