<?php
declare(strict_types=1);

namespace Megio\Http\Request\Collection;

use Doctrine\DBAL\Exception\ConstraintViolationException;
use Megio\Collection\Exception\SerializerException;
use Megio\Collection\RecipeRequest;
use Megio\Collection\WriteBuilder\WriteBuilder;
use Megio\Collection\WriteBuilder\WriteBuilderEvent;
use Megio\Collection\Exception\CollectionException;
use Megio\Collection\Mapping\ArrayToEntity;
use Megio\Collection\RecipeFinder;
use Megio\Database\Entity\EntityException;
use Megio\Event\Collection\EventType;
use Megio\Http\Request\Request;
use Nette\Schema\Expect;
use Megio\Database\EntityManager;
use Megio\Event\Collection\Events;
use Megio\Event\Collection\OnStartEvent;
use Megio\Event\Collection\OnExceptionEvent;
use Megio\Event\Collection\OnFinishEvent;
use Symfony\Component\HttpFoundation\Response;

class CreateRequest extends Request
{
    public function __construct(
        protected readonly EntityManager $em,
        protected readonly RecipeFinder  $recipeFinder,
        protected readonly WriteBuilder  $builder,
    )
    {
    }
    
    public function schema(): array
    {
        $recipeKeys = array_map(fn($r) => $r->key(), $this->recipeFinder->load()->getAll());
        
        return [
            'recipe' => Expect::anyOf(...$recipeKeys)->required(),
            'rows' => Expect::arrayOf(
                Expect::arrayOf('int|float|string|bool|null|array', 'string')->min(1)->required()
            )->min(1)->max(1000)->required(),
            'custom_data' => Expect::arrayOf('int|float|string|bool|null|array', 'string')->nullable()->default([]),
        ];
    }
    
    /**
     * @throws \Doctrine\ORM\Exception\ORMException
     */
    public function process(array $data): Response
    {
        /** @noinspection DuplicatedCode */
        $recipe = $this->recipeFinder->findByKey($data['recipe']);
        
        if ($recipe === null) {
            return $this->error(["Collection '{$data['recipe']}' not found"]);
        }
        
        $event = new OnStartEvent(EventType::CREATE, $data, $recipe, $this->request);
        $dispatcher = $this->dispatcher->dispatch($event, Events::ON_START->value);
        
        if ($dispatcher->getResponse()) {
            return $dispatcher->getResponse();
        }
        
        $ids = [];
        
        foreach ($data['rows'] as $row) {
            $recipeRequest = new RecipeRequest($this->request, false, null, $row, $data['custom_data']);
            
            try {
                $builder = $recipe->create($this->builder->create($recipe, WriteBuilderEvent::CREATE, null, $row), $recipeRequest)->build();
            } catch (CollectionException $e) {
                $response = $this->error([$e->getMessage()], 406);
                $event = new OnExceptionEvent(EventType::CREATE, $data, $recipe, $e, $this->request, $response);
                $dispatcher = $this->dispatcher->dispatch($event, Events::ON_EXCEPTION->value);
                return $dispatcher->getResponse();
            }
            
            /** @noinspection DuplicatedCode */
            $builder->validate();
            
            if (!$builder->isValid()) {
                $response = $this->json(['errors' => [], 'validation_errors' => $builder->getErrors()], 400);
                $e = new CollectionException('Invalid data');
                $event = new OnExceptionEvent(EventType::CREATE, $data, $recipe, $e, $this->request, $response);
                $dispatcher = $this->dispatcher->dispatch($event, Events::ON_EXCEPTION->value);
                return $dispatcher->getResponse();
            }
            
            try {
                $values = $builder->getSerializedValues();
                $entity = ArrayToEntity::create($recipe, $builder->getMetadata(), $values);
                $this->em->persist($entity);
                $ids[] = $entity->getId();
            } catch (CollectionException|SerializerException|EntityException $e) {
                $response = $this->error([$e->getMessage()], 406);
                $event = new OnExceptionEvent(EventType::CREATE, $data, $recipe, $e, $this->request, $response);
                $dispatcher = $this->dispatcher->dispatch($event, Events::ON_EXCEPTION->value);
                return $dispatcher->getResponse();
            }
        }
        
        /** @noinspection DuplicatedCode */
        $this->em->beginTransaction();
        
        try {
            foreach (ArrayToEntity::getEntitiesToFlush()->getIterator() as $entity) {
                $this->em->flush($entity);
            }
            $this->em->commit();
        } catch (ConstraintViolationException $e) {
            $this->em->rollback();
            $response = $this->error([$e->getMessage()]);
            $event = new OnExceptionEvent(EventType::CREATE, $data, $recipe, $e, $this->request, $response);
            $dispatcher = $this->dispatcher->dispatch($event, Events::ON_EXCEPTION->value);
            return $dispatcher->getResponse();
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
        
        $result = [
            'ids' => $ids,
            'message' => "Items successfully created"
        ];
        
        $response = $this->json($result);
        
        $event = new OnFinishEvent(EventType::CREATE, $data, $recipe, $result, $this->request, $response);
        $dispatcher = $this->dispatcher->dispatch($event, Events::ON_FINISH->value);
        
        return $dispatcher->getResponse();
    }
}