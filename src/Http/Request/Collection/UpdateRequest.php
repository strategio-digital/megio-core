<?php
declare(strict_types=1);

namespace Megio\Http\Request\Collection;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Megio\Collection\WriteBuilder\WriteBuilder;
use Megio\Collection\WriteBuilder\WriteBuilderEvent;
use Megio\Collection\Exception\CollectionException;
use Megio\Collection\Mapping\ArrayToEntity;
use Megio\Collection\RecipeFinder;
use Megio\Http\Request\Request;
use Nette\Schema\Expect;
use Megio\Database\Entity\EntityException;
use Megio\Database\EntityManager;
use Megio\Event\Collection\CollectionEvent;
use Megio\Event\Collection\OnProcessingFinishEvent;
use Megio\Event\Collection\OnProcessingStartEvent;
use Megio\Event\Collection\OnProcessingExceptionEvent;
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
    
    public function schema(): array
    {
        $names = array_map(fn($r) => $r->name(), $this->recipeFinder->load()->getAll());
        
        return [
            'recipe' => Expect::anyOf(...$names)->required(),
            'rows' => Expect::arrayOf(
                Expect::structure([
                    'id' => Expect::string()->required(),
                    'data' => Expect::arrayOf('int|float|string|bool|null|array', 'string')->min(1)->required()
                ])->castTo('array')
            )->min(1)->required()
        ];
    }
    
    /**
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Exception
     */
    public function process(array $data): Response
    {
        /** @noinspection DuplicatedCode */
        $recipe = $this->recipeFinder->findByName($data['recipe']);
        
        if ($recipe === null) {
            return $this->error(["Collection '{$data['recipe']}' not found"]);
        }
        
        $event = new OnProcessingStartEvent($data, $this->request, $recipe);
        $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_START);
        
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
                $event = new OnProcessingExceptionEvent($data, $this->request, $recipe, $e, $response);
                $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_EXCEPTION);
                return $dispatcher->getResponse();
            }
            
            try {
                $builder = $recipe
                    ->update($this->builder->create($recipe, WriteBuilderEvent::UPDATE, $row['data'], $row['id']))
                    ->build();
            } catch (CollectionException $e) {
                $response = $this->error([$e->getMessage()], 406);
                $event = new OnProcessingExceptionEvent($data, $this->request, $recipe, $e, $response);
                $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_EXCEPTION);
                return $dispatcher->getResponse();
            }
            
            /** @noinspection DuplicatedCode */
            $builder->validate();
            
            if (!$builder->isValid()) {
                $response = $this->json(['validation_errors' => $builder->getErrors()], 400);
                $event = new OnProcessingExceptionEvent($data, $this->request, $recipe, new CollectionException('Invalid data'), $response);
                $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_EXCEPTION);
                return $dispatcher->getResponse();
            }
            
            try {
                ArrayToEntity::update($builder->getMetadata(), $item, $builder->toClearValues());
            } catch (CollectionException|EntityException $e) {
                $response = $this->error([$e->getMessage()], 406);
                $event = new OnProcessingExceptionEvent($data, $this->request, $recipe, $e, $response);
                $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_EXCEPTION);
                return $dispatcher->getResponse();
            }
        }
        
        /** @noinspection DuplicatedCode */
        $this->em->beginTransaction();
        
        try {
            $this->em->flush();
            $this->em->commit();
        } catch (UniqueConstraintViolationException $e) {
            $this->em->rollback();
            $response = $this->error([$e->getMessage()]);
            $event = new OnProcessingExceptionEvent($data, $this->request, $recipe, $e, $response);
            $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_EXCEPTION);
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
        
        $event = new OnProcessingFinishEvent($data, $this->request, $recipe, $result, $response);
        $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_FINISH);
        
        return $dispatcher->getResponse();
    }
}