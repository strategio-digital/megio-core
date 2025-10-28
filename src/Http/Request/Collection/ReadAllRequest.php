<?php
declare(strict_types=1);

namespace Megio\Http\Request\Collection;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Megio\Collection\CollectionRequest;
use Megio\Collection\Exception\CollectionException;
use Megio\Collection\ReadBuilder\Column\Base\IColumn;
use Megio\Collection\ReadBuilder\ReadBuilder;
use Megio\Collection\ReadBuilder\ReadBuilderEvent;
use Megio\Collection\RecipeFinder;
use Megio\Collection\SchemaFormatter;
use Megio\Collection\SearchBuilder\SearchBuilder;
use Megio\Database\EntityManager;
use Megio\Event\Collection\Events;
use Megio\Event\Collection\EventType;
use Megio\Event\Collection\OnFinishEvent;
use Megio\Event\Collection\OnStartEvent;
use Megio\Http\Request\Request;
use Nette\Schema\Expect;
use Symfony\Component\HttpFoundation\Response;

class ReadAllRequest extends Request
{
    public function __construct(
        protected readonly EntityManager $em,
        protected readonly RecipeFinder $recipeFinder,
        protected readonly ReadBuilder $readBuilder,
        protected readonly SearchBuilder $searchBuilder,
    ) {}

    public function schema(array $data): array
    {
        $recipes = $this->recipeFinder->load()->getAll();

        $recipeKeys = array_map(fn(
            $r,
        ) => $r->key(), $recipes);

        return [
            'recipeKey' => Expect::anyOf(...$recipeKeys)->required(),
            'schema' => Expect::bool(false),
            'adminPanel' => Expect::bool(false),
            'currentPage' => Expect::int(1)->min(1)->required(),
            'itemsPerPage' => Expect::int(10)->max(1000)->required(),
            'search' => Expect::structure([
                'text' => Expect::string()->nullable()->default(null),
                'filters' => Expect::arrayOf(
                    Expect::structure([
                        'col' => Expect::string()->required(),
                        'operator' => Expect::anyOf('AND', 'OR')->required(),
                        'value' => Expect::mixed()->required(),
                    ])->castTo('array'),
                )->min(0)->default([]),
            ])->castTo('array'),
            'orderBy' => Expect::arrayOf(
                Expect::structure([
                    'col' => Expect::string()->required(),
                    'desc' => Expect::bool()->required(),
                ])->castTo('array'),
            )->min(0)->default([]),
            'custom_data' => Expect::arrayOf('int|float|string|bool|null|array', 'string')->nullable()->default([]),
        ];
    }

    /**
     * @throws NonUniqueResultException
     * @throws NotSupported
     * @throws NoResultException
     */
    public function process(array $data): Response
    {
        $recipe = $this->recipeFinder->findByKey($data['recipeKey']);

        if ($recipe === null) {
            return $this->error(["Collection '{$data['recipeKey']}' not found"]);
        }

        $collectionRequest = new CollectionRequest($this->request, false, $data, null, []);

        try {
            $defaultBuilder = $this->readBuilder->create($recipe, ReadBuilderEvent::READ_ALL);
            $builder = $recipe->readAll($defaultBuilder, $collectionRequest)->build();
        } catch (CollectionException $e) {
            return $this->error([$e->getMessage()]);
        }

        /** @noinspection DuplicatedCode */
        if ($builder->countFields() === 1) {
            return $this->error(["Collection '{$data['recipe']}' has no readable fields"]);
        }

        $event = new OnStartEvent(EventType::READ_ALL, $data, $recipe, $this->request);
        $dispatcher = $this->dispatcher->dispatch($event, Events::ON_START->value);

        if ($dispatcher->getResponse()) {
            return $dispatcher->getResponse();
        }

        $repo = $this->em->getRepository($recipe->source());

        $countQb = $repo->createQueryBuilder('entity')->select('count(entity.id)');

        // Search
        if (array_key_exists('search', $data)) {
            $sb = $this->searchBuilder->create($countQb, $collectionRequest);
            $countQb = $recipe->search($sb, $sb->getRequest())->build();
        }

        // Count
        $count = $countQb->getQuery()->getSingleScalarResult();

        $qb = $builder
            ->createQueryBuilder($repo, 'entity')
            ->setFirstResult(($data['currentPage'] - 1) * $data['itemsPerPage'])
            ->setMaxResults($data['itemsPerPage']);

        // Search
        $sb = $this->searchBuilder->create($qb, $collectionRequest);
        if (array_key_exists('search', $data)) {
            $qb = $recipe->search($sb, $sb->getRequest())->build();
        }

        // Sortable columns
        $sortable = array_filter($builder->getColumns(), fn(
            IColumn $col,
        ) => $col->isSortable());

        $sortableKeys = array_map(fn(
            IColumn $col,
        ) => $col->getKey(), $sortable);

        $responseSort = [];

        // Order by only by sortable columns
        foreach ($data['orderBy'] as $param) {
            if (in_array($param['col'], $sortableKeys, true)) {
                $qb->addOrderBy("entity.{$param['col']}", $param['desc'] ? 'DESC' : 'ASC');
                $responseSort[] = [
                    'col' => $param['col'],
                    'desc' => $param['desc'],
                ];
            }
        }

        if (count($data['orderBy']) === 0) {
            foreach ($recipe->sort() as $columnName => $direction) {
                $qb->addOrderBy("entity.{$columnName}", $direction);
                $responseSort[] = [
                    'col' => $columnName,
                    'desc' => $direction === 'DESC',
                ];
            }
        }

        $qb->addOrderBy('entity.id', 'ASC');

        $query = $qb->getQuery()->setHydrationMode(AbstractQuery::HYDRATE_ARRAY);
        $paginator = new Paginator($query, true);
        $items = iterator_to_array($paginator->getIterator());

        foreach ($items as $key => $item) {
            $items[$key] = $builder->format($item, $data['adminPanel']);
        }

        $result = [
            'pagination' => [
                'currentPage' => $data['currentPage'],
                'lastPage' => (int)ceil($count / $data['itemsPerPage']),
                'itemsPerPage' => $data['itemsPerPage'],
                'itemsCountAll' => $count,
                'orderBy' => $responseSort,
            ],
            'items' => $items,
        ];

        if ($data['schema']) {
            $result['schema'] = SchemaFormatter::format($recipe, $builder);
            $result['schema']['search'] = $sb->toArray();
        }

        $response = $this->json($result);

        $event = new OnFinishEvent(EventType::READ_ALL, $data, $recipe, $result, $this->request, $response);
        $dispatcher = $this->dispatcher->dispatch($event, Events::ON_FINISH->value);

        return $dispatcher->getResponse();
    }
}
