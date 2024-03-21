<?php
declare(strict_types=1);

namespace Megio\Http\Request\Collection\Form;

use Doctrine\ORM\AbstractQuery;
use Megio\Collection\Exception\CollectionException;
use Megio\Collection\WriteBuilder\WriteBuilder;
use Megio\Collection\WriteBuilder\WriteBuilderEvent;
use Megio\Collection\RecipeFinder;
use Megio\Database\EntityManager;
use Megio\Http\Request\Request;
use Nette\Schema\Expect;
use Symfony\Component\HttpFoundation\Response;

class EditFormRequest extends Request
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
            'id' => Expect::string()->required(),
        ];
    }
    
    public function process(array $data): Response
    {
        $recipe = $this->recipeFinder->findByName($data['recipe']);
        
        if ($recipe === null) {
            return $this->error(["Collection '{$data['recipe']}' not found"]);
        }
        
        // TODO: add relations mapping & joins
        
        /** @var array<string, string|int|float|bool|null>|null $row */
        $row = $this->em->getRepository($recipe->source())
            ->createQueryBuilder('entity')
            ->where('entity.id = :id')
            ->setParameter('id', $data['id'])
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
        
        if ($row === null) {
            return $this->error(["Item '{$data['id']}' not found"], 404);
        }
        
        /** @var string $rowId */
        $rowId = $row['id'];
        
        try {
            $builder = $recipe
                ->update($this->builder->create($recipe, WriteBuilderEvent::UPDATE, $row, $rowId))
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