<?php
declare(strict_types=1);

namespace Megio\Http\Request\Collection\Form;

use Doctrine\ORM\AbstractQuery;
use Megio\Collection\CollectionRequest;
use Megio\Collection\Exception\CollectionException;
use Megio\Collection\ReadBuilder\ReadBuilder;
use Megio\Collection\ReadBuilder\ReadBuilderEvent;
use Megio\Collection\WriteBuilder\WriteBuilder;
use Megio\Collection\WriteBuilder\WriteBuilderEvent;
use Megio\Collection\RecipeFinder;
use Megio\Database\EntityManager;
use Megio\Event\Collection\Events;
use Megio\Event\Collection\OnFormStartEvent;
use Megio\Http\Request\Request;
use Nette\Schema\Expect;
use Symfony\Component\HttpFoundation\Response;

class UpdatingFormRequest extends Request
{
    public function __construct(
        protected readonly EntityManager $em,
        protected readonly RecipeFinder  $recipeFinder,
        protected readonly WriteBuilder  $writeBuilder,
        protected readonly ReadBuilder   $readBuilder,
    )
    {
    }
    
    public function schema(array $data): array
    {
        $recipeKeys = array_map(fn($r) => $r->key(), $this->recipeFinder->load()->getAll());
        
        return [
            'recipeKey' => Expect::anyOf(...$recipeKeys)->required(),
            'id' => Expect::string()->required(),
            'custom_data' => Expect::arrayOf('int|float|string|bool|null|array', 'string')->nullable()->default([]),
        ];
    }
    
    public function process(array $data): Response
    {
        $recipe = $this->recipeFinder->findByKey($data['recipeKey']);
        
        if ($recipe === null) {
            return $this->error(["Collection '{$data['recipeKey']}' not found"]);
        }
        
        $event = new OnFormStartEvent(false, $data, $recipe, $this->request);
        $dispatcher = $this->dispatcher->dispatch($event, Events::ON_FORM_START->value);
        
        if ($dispatcher->getResponse()) {
            return $dispatcher->getResponse();
        }
        
        $collectionRequest = new CollectionRequest($this->request, true, $data, null, []);
        
        try {
            $defaultBuilder = $this->readBuilder->create($recipe, ReadBuilderEvent::READ_ONE);
            $readBuilder = $recipe->read($defaultBuilder, $collectionRequest)->build();
            $schema = $recipe->getEntityMetadata()->getFullSchemaReflectedByDoctrine();
            $repo = $this->em->getRepository($recipe->source());
        } catch (CollectionException $e) {
            return $this->error([$e->getMessage()]);
        }
        
        $qb = $readBuilder
            ->createQueryBuilder($repo, 'entity')
            ->where('entity.id = :id')
            ->setParameter('id', $data['id']);
        
        /** @var array<string, string|int|float|bool|null>|null $row */
        $row = $qb
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
        
        if ($row === null) {
            return $this->error(["Item '{$data['id']}' not found"], 404);
        }
        
        // Format one-to-one data
        foreach ($schema->getOneToOneColumns() as $column) {
            if ($row[$column['name']] !== null) {
                $row[$column['name']] = $row[$column['name']]['id'];
            }
        }
        
        // Format one-to-many data
        foreach ($schema->getOneToManyColumns() as $column) {
            if (array_key_exists($column['name'], $row)) {
                $row[$column['name']] = array_map(fn($item) => $item['id'], $row[$column['name']]);
            }
        }
        
        // Format many-to-one data
        foreach ($schema->getManyToOneColumns() as $column) {
            if ($row[$column['name']] !== null) {
                $row[$column['name']] = $row[$column['name']]['id'];
            }
        }
        
        // Format-many-to-many data
        foreach ($schema->getManyToManyColumns() as $column) {
            if (array_key_exists($column['name'], $row)) {
                $row[$column['name']] = array_map(fn($item) => $item['id'], $row[$column['name']]);
            }
        }
        
        /** @var string $rowId */
        $rowId = $row['id'];
        $collectionRequest = new CollectionRequest($this->request, true, $data, $rowId, $row);
        
        try {
            $defaultBuilder = $this->writeBuilder->create($recipe, WriteBuilderEvent::UPDATE, $rowId, $row);
            $builder = $recipe->update($defaultBuilder, $collectionRequest)->build();
        } catch (CollectionException $e) {
            return $this->error([$e->getMessage()]);
        }
        
        if ($builder->countFields() === 0) {
            return $this->error(["Collection '{$data['recipe']}' has no editable fields"]);
        }
        
        return $this->json([
            'recipe' => [
                'key' => $recipe->key(),
                'name' => $recipe->name(),
            ],
            'form' => $builder->toArray()
        ]);
    }
}