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
use Saas\Database\Entity\Admin;
use Saas\Database\Entity\Auth\Resource;
use Saas\Database\Entity\Auth\Role;
use Saas\Database\Entity\Auth\Token;
use Saas\Database\EntityManager;
use Saas\Database\Interface\ICrudable;

class CrudHelper
{
    /** @var class-string[] */
    const EXCLUDED_EVERYWHERE = [Role::class, Resource::class, Token::class];
    
    /** @var class-string[] */
    const INVISIBLE_IN_COLLECTION_NAV = [Admin::class];
    
    const
        PROPERTY_SHOW_ALL = 'showAllFields',
        PROPERTY_SHOW_ONE = 'showOneFields',
        PROPERTY_INVISIBLE = 'invisibleFields';
    
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
    public function getAllEntities(): array
    {
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        
        $entities = array_map(function (ClassMetadata $metadata) {
            $attr = $metadata->getReflectionClass()->getAttributes(Table::class)[0]->newInstance();
            return ['table' => str_replace('`', '', $attr->name ?? ''), 'value' => $metadata->name];
        }, $metadata);
        
        return array_filter($entities, fn($entity) => !in_array($entity['value'], self::EXCLUDED_EVERYWHERE));
    }
    
    /**
     * @param string $tableName
     * @return class-string|null
     */
    public function getEntityClassName(string $tableName): ?string
    {
        $entityNames = $this->getAllEntities();
        $current = current(array_filter($entityNames, fn($namespace) => $namespace['table'] === $tableName));
        return $current ? $current['value'] : null;
    }
    
    /**
     * @param class-string $entityClassName
     * @param string $propertyName
     * @return array<int, string>
     */
    public function getPropertyDefaults(string $entityClassName, string $propertyName): array
    {
        try {
            $ref = new \ReflectionClass($entityClassName);
            return $ref->getProperty($propertyName)->getDefaultValue();
        } catch (\ReflectionException) {
            return [];
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
     * @param string $visiblePropsProperty
     * @param bool $schema
     * @return \Saas\Database\CrudHelper\EntityMetadata|null
     */
    public function getEntityMetadata(string $tableName, string $visiblePropsProperty, bool $schema = false): ?EntityMetadata
    {
        $this->error = null;
        
        $className = $this->getEntityClassName($tableName);
        
        if (!$className) {
            $this->error = "Collection '{$tableName}' not found";
            return null;
        }
        
        $visibleProps = $this->getPropertyDefaults($className, $visiblePropsProperty);
        $visibleProps = array_merge(['id'], array_filter($visibleProps, fn($prop) => $prop !== 'id'));
        
        if (count($visibleProps) === 1) { // 'id' is automatically included
            $this->error = "Collection '{$tableName}' has no visible fields";
            return null;
        }
        
        $fieldsSchema = [];
        $invisibleFields = [];
        
        if ($schema) {
            $schemaProps = $this->getEntitySchema($className);
            $fieldsSchema = array_values(array_filter($schemaProps, fn($prop) => in_array($prop['name'], $visibleProps)));
            $invisibleFields = $this->getPropertyDefaults($className, self::PROPERTY_INVISIBLE);
        }
        
        
        return new EntityMetadata($className, $tableName, $visibleProps, $fieldsSchema, $invisibleFields);
    }
    
    /**
     * @param \Saas\Database\Interface\ICrudable $entity
     * @param array<string, mixed> $props
     * @return \Saas\Database\Interface\ICrudable
     * @throws \Saas\Database\CrudHelper\CrudException
     */
    public function setUpEntityProps(ICrudable $entity, array $props): ICrudable
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