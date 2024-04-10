<?php
declare(strict_types=1);

namespace Megio\Http\Request\Collection\Form;

use Doctrine\ORM\AbstractQuery;
use Megio\Collection\Exception\CollectionException;
use Megio\Collection\ReadBuilder\ReadBuilder;
use Megio\Collection\ReadBuilder\ReadBuilderEvent;
use Megio\Collection\RecipeRequest;
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
    
    public function schema(): array
    {
        $recipeKeys = array_map(fn($r) => $r->key(), $this->recipeFinder->load()->getAll());
        
        return [
            'recipe' => Expect::anyOf(...$recipeKeys)->required(),
            'id' => Expect::string()->required(),
            'custom_data' => Expect::arrayOf('int|float|string|bool|null|array', 'string')->nullable()->default([]),
        ];
    }
    
    public function process(array $data): Response
    {
        $recipe = $this->recipeFinder->findByKey($data['recipe']);
        
        if ($recipe === null) {
            return $this->error(["Collection '{$data['recipe']}' not found"]);
        }
        
        $event = new OnFormStartEvent(false, $data, $recipe, $this->request);
        $dispatcher = $this->dispatcher->dispatch($event, Events::ON_FORM_START->value);
        
        if ($dispatcher->getResponse()) {
            return $dispatcher->getResponse();
        }
        
        $recipeRequest = new RecipeRequest($this->request, true, null, [], $data['custom_data']);
        
        try {
            $readBuilder = $recipe->read($this->readBuilder->create($recipe, ReadBuilderEvent::READ_ONE), $recipeRequest)->build();
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
            $row[$column['name']] = array_map(fn($item) => $item['id'], $row[$column['name']]);
        }
        
        // Format many-to-one data
        foreach ($schema->getManyToOneColumns() as $column) {
            if ($row[$column['name']] !== null) {
                $row[$column['name']] = $row[$column['name']]['id'];
            }
        }
        
        // Format-many-to-many data
        foreach ($schema->getManyToManyColumns() as $column) {
            $row[$column['name']] = array_map(fn($item) => $item['id'], $row[$column['name']]);
        }
        
        /** @var string $rowId */
        $rowId = $row['id'];
        $recipeRequest = new RecipeRequest($this->request, true, $rowId, $row, $data['custom_data']);
        
        try {
            $builder = $recipe
                ->update($this->writeBuilder->create($recipe, WriteBuilderEvent::UPDATE, $rowId, $row), $recipeRequest)
                ->build();
        } catch (CollectionException $e) {
            return $this->error([$e->getMessage()]);
        }
        
        if ($builder->countFields() === 0) {
            return $this->error(["Collection '{$data['recipe']}' has no editable fields"]);
        }
        
        return $this->json(['form' => $builder->toArray()]);
    }
}