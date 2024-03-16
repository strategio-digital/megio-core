<?php
declare(strict_types=1);

namespace Megio\Http\Request\Collection;

use Doctrine\ORM\AbstractQuery;
use Megio\Collection\CollectionException;
use Megio\Collection\CollectionPropType;
use Megio\Collection\RecipeFinder;
use Megio\Http\Request\Request;
use Nette\Schema\Expect;
use Megio\Database\EntityManager;
use Megio\Event\Collection\CollectionEvent;
use Megio\Event\Collection\OnProcessingExceptionEvent;
use Megio\Event\Collection\OnProcessingStartEvent;
use Megio\Event\Collection\OnProcessingFinishEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShowOneRequest extends Request
{
    public function __construct(
        protected readonly EntityManager $em,
        protected readonly RecipeFinder  $recipeFinder
    )
    {
    }
    
    public function schema(): array
    {
        $names = array_map(fn($r) => $r->name(), $this->recipeFinder->load()->getAll());
        
        return [
            'recipe' => Expect::anyOf(...$names)->required(),
            'schema' => Expect::bool(false),
            'id' => Expect::string()->required(),
        ];
    }
    
    public function process(array $data): Response
    {
        $recipe = $this->recipeFinder->findByName($data['recipe']);
        
        if ($recipe === null) {
            return $this->error(["Collection {$data['recipe']} not found"]);
        }
        
        try {
            $metadata = $recipe->getEntityMetadata(CollectionPropType::SHOW_ONE);
        } catch (CollectionException $e) {
            return $this->error([$e->getMessage()]);
        }
        
        $event = new OnProcessingStartEvent($data, $this->request, $metadata);
        $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_START);
        
        if ($dispatcher->getResponse()) {
            return $dispatcher->getResponse();
        }
        
        $repo = $this->em->getRepository($recipe->source());
        
        $qb = $repo->createQueryBuilder('entity')
            ->select($metadata->getQbSelect('entity'))
            ->where('entity.id = :id')
            ->setParameter('id', $data['id']);
        
        $item = $qb->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
        
        if (!$item) {
            $e = new NotFoundHttpException("Item '{$data['id']}' not found");
            $response = $this->error([$e->getMessage()], 404);
            $event = new OnProcessingExceptionEvent($data, $this->request, $metadata, $e, $response);
            $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_EXCEPTION);
            return $dispatcher->getResponse();
        }
        
        $result = ['item' => $item];
        
        if ($data['schema']) {
            $result['schema'] = $metadata->getSchema();
        }
        
        $response = $this->json($result);
        
        $event = new OnProcessingFinishEvent($data, $this->request, $metadata, $result, $response);
        $dispatcher = $this->dispatcher->dispatch($event, CollectionEvent::ON_PROCESSING_FINISH);
        
        return $dispatcher->getResponse();
    }
}