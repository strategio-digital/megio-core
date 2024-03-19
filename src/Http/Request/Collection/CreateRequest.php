<?php
declare(strict_types=1);

namespace Megio\Http\Request\Collection;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\Exception\ORMException;
use Megio\Collection\Builder\Builder;
use Megio\Collection\Builder\BuilderEventName;
use Megio\Collection\CollectionException;
use Megio\Collection\CollectionPropType;
use Megio\Collection\Mapping\ArrayToEntity;
use Megio\Collection\RecipeFinder;
use Megio\Http\Request\Request;
use Nette\Schema\Expect;
use Megio\Database\EntityManager;
use Megio\Event\Collection\CollectionEvent;
use Megio\Event\Collection\OnProcessingStartEvent;
use Megio\Event\Collection\OnProcessingExceptionEvent;
use Megio\Event\Collection\OnProcessingFinishEvent;
use Symfony\Component\HttpFoundation\Response;

class CreateRequest extends Request
{
    public function __construct(
        protected readonly EntityManager $em,
        protected readonly RecipeFinder  $recipeFinder,
        protected readonly Builder       $builder,
    )
    {
    }
    
    public function schema(): array
    {
        $names = array_map(fn($r) => $r->name(), $this->recipeFinder->load()->getAll());
        
        return [
            'recipe' => Expect::anyOf(...$names)->required(),
            'rows' => Expect::arrayOf(
                Expect::arrayOf('int|float|string|bool|null', 'string')->min(1)->required()
            )->min(1)->max(1000)->required()
        ];
    }
    
    public function process(array $data): Response
    {
        $recipe = $this->recipeFinder->findByName($data['recipe']);
        
        if ($recipe === null) {
            return $this->error(["Collection '{$data['recipe']}' not found"]);
        }
        
        try {
            $metadata = $recipe->getEntityMetadata(CollectionPropType::NONE);
        } catch (CollectionException $e) {
            return $this->error([$e->getMessage()]);
        }
        
        $event = new OnProcessingStartEvent($data, $this->request, $metadata);
        $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_START);
        
        if ($dispatcher->getResponse()) {
            return $dispatcher->getResponse();
        }
        
        $ids = [];
        
        foreach ($data['rows'] as $row) {
            $builder = $recipe->create($this->builder->create($recipe, BuilderEventName::CREATE, $row))
                ->build()
                ->validate();
            
            if (!$builder->isValid()) {
                $response = $this->json(['validation_errors' => $builder->getErrors()], 400);
                $event = new OnProcessingExceptionEvent($data, $this->request, $metadata, new CollectionException('Invalid data'), $response);
                $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_EXCEPTION);
                return $dispatcher->getResponse();
            }
            
            try {
                $entity = ArrayToEntity::create($recipe, $metadata, $builder->toClearValues());
                $this->em->persist($entity);
                $ids[] = $entity->getId();
            } catch (CollectionException|ORMException $e) {
                $response = $this->error([$e->getMessage()], 406);
                $event = new OnProcessingExceptionEvent($data, $this->request, $metadata, $e, $response);
                $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_EXCEPTION);
                return $dispatcher->getResponse();
            }
        }
        
        $this->em->beginTransaction();
        
        try {
            $this->em->flush();
            $this->em->commit();
        } catch (UniqueConstraintViolationException $e) {
            $this->em->rollback();
            $response = $this->error([$e->getMessage()]);
            $event = new OnProcessingExceptionEvent($data, $this->request, $metadata, $e, $response);
            $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_EXCEPTION);
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
        
        $event = new OnProcessingFinishEvent($data, $this->request, $metadata, $result, $response);
        $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_FINISH);
        
        return $dispatcher->getResponse();
    }
}