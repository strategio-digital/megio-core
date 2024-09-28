<?php
declare(strict_types=1);

namespace Megio\Http\Request\Collection;

use Doctrine\DBAL\Exception\ConstraintViolationException;
use Megio\Collection\CollectionRequest;
use Megio\Collection\Exception\SerializerException;
use Megio\Collection\WriteBuilder\WriteBuilder;
use Megio\Collection\WriteBuilder\WriteBuilderEvent;
use Megio\Collection\Exception\CollectionException;
use Megio\Collection\Mapping\ArrayToEntity;
use Megio\Collection\RecipeFinder;
use Megio\Event\Collection\EventType;
use Megio\Http\Request\Request;
use Nette\Schema\Expect;
use Megio\Database\Entity\EntityException;
use Megio\Database\EntityManager;
use Megio\Event\Collection\Events;
use Megio\Event\Collection\OnFinishEvent;
use Megio\Event\Collection\OnStartEvent;
use Megio\Event\Collection\OnExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateRequest extends Request
{
    public function __construct(
        protected readonly EntityManager $em,
        protected readonly RecipeFinder  $recipeFinder,
        protected readonly WriteBuilder  $builder,
    )
    {
    }
    
    public function schema(array $data): array
    {
        $recipeKeys = array_map(fn($r) => $r->key(), $this->recipeFinder->load()->getAll());
        
        return [
            'recipe' => Expect::anyOf(...$recipeKeys)->required(),
            'rows' => Expect::arrayOf(
                Expect::structure([
                    'id' => Expect::string()->required(),
                    'data' => Expect::arrayOf('int|float|string|bool|null|array', 'string')->min(1)->required()
                ])->castTo('array')
            )->min(1)->required(),
            'custom_data' => Expect::arrayOf('int|float|string|bool|null|array', 'string')->nullable()->default([]),
        ];
    }
    
    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Exception
     */
    public function process(array $data): Response
    {
        /** @noinspection DuplicatedCode */
        $recipe = $this->recipeFinder->findByKey($data['recipe']);
        
        if ($recipe === null) {
            return $this->error(["Collection '{$data['recipe']}' not found"]);
        }
        
        $event = new OnStartEvent(EventType::UPDATE, $data, $recipe, $this->request);
        $dispatcher = $this->dispatcher->dispatch($event, Events::ON_START->value);
        
        if ($dispatcher->getResponse()) {
            return $dispatcher->getResponse();
        }
        
        $ids = array_map(fn($row) => $row['id'], $data['rows']);
        
        $qb = $this->em->getRepository($recipe->source())
            ->createQueryBuilder('entity')
            ->select('entity')
            ->where('entity.id IN (:ids)')
            ->setParameter('ids', $ids);
        
        /** @var \Megio\Database\Interface\ICrudable[] $rows */
        $rows = $qb->getQuery()->getResult();
        
        foreach ($data['rows'] as $row) {
            $item = current(array_filter($rows, fn($db) => $db->getId() === $row['id']));
            
            /** @noinspection DuplicatedCode */
            if (!$item) {
                $e = new NotFoundHttpException("Item '{$row['id']}' not found");
                $response = $this->error([$e->getMessage()], 404);
                $event = new OnExceptionEvent(EventType::UPDATE, $data, $recipe, $e, $this->request, $response);
                $dispatcher = $this->dispatcher->dispatch($event, Events::ON_EXCEPTION->value);
                return $dispatcher->getResponse();
            }
            
            $collectionRequest = new CollectionRequest($this->request, false, $data, $row['id'], $row['data']);
            
            try {
                $builder = $recipe
                    ->update($this->builder->create($recipe, WriteBuilderEvent::UPDATE, $row['id'], $row['data']), $collectionRequest)
                    ->build();
            } catch (CollectionException $e) {
                $response = $this->error([$e->getMessage()], 406);
                $event = new OnExceptionEvent(EventType::UPDATE, $data, $recipe, $e, $this->request, $response);
                $dispatcher = $this->dispatcher->dispatch($event, Events::ON_EXCEPTION->value);
                return $dispatcher->getResponse();
            }
            
            /** @noinspection DuplicatedCode */
            $builder->validate();
            
            if (!$builder->isValid()) {
                $response = $this->json(['errors' => [], 'validation_errors' => $builder->getErrors()], 400);
                $e = new CollectionException('Invalid data');
                $event = new OnExceptionEvent(EventType::UPDATE, $data, $recipe, $e, $this->request, $response);
                $dispatcher = $this->dispatcher->dispatch($event, Events::ON_EXCEPTION->value);
                return $dispatcher->getResponse();
            }
            
            try {
                $values = $builder->getSerializedValues();
                ArrayToEntity::update($builder->getMetadata(), $item, $values);
            } catch (CollectionException|SerializerException|EntityException $e) {
                $response = $this->error([$e->getMessage()], 406);
                $event = new OnExceptionEvent(EventType::UPDATE, $data, $recipe, $e, $this->request, $response);
                $dispatcher = $this->dispatcher->dispatch($event, Events::ON_EXCEPTION->value);
                return $dispatcher->getResponse();
            }
        }
        
        /** @noinspection DuplicatedCode */
        $this->em->beginTransaction();
        
        try {
            foreach (ArrayToEntity::getEntitiesToFlush()->getIterator() as $entity) {
                $classMetadata = $this->em->getClassMetadata($entity::class);
                $this->em->getUnitOfWork()->recomputeSingleEntityChangeSet($classMetadata, $entity);
                $this->em->flush($entity);
            }
            $this->em->commit();
        } catch (ConstraintViolationException $e) {
            $this->em->rollback();
            $response = $this->error([$e->getMessage()]);
            $event = new OnExceptionEvent(EventType::UPDATE, $data, $recipe, $e, $this->request, $response);
            $dispatcher = $this->dispatcher->dispatch($event, Events::ON_EXCEPTION->value);
            return $dispatcher->getResponse();
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
        
        $result = [
            'ids' => $ids,
            'message' => "Items successfully updated"
        ];
        
        $response = $this->json($result);
        
        $event = new OnFinishEvent(EventType::UPDATE, $data, $recipe, $result, $this->request, $response);
        $dispatcher = $this->dispatcher->dispatch($event, Events::ON_FINISH->value);
        
        return $dispatcher->getResponse();
    }
}