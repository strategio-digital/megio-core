<?php
declare(strict_types=1);

namespace Megio\Http\Request\Collection\Form;

use Megio\Collection\Builder\Builder;
use Megio\Collection\Builder\BuilderEventName;
use Megio\Collection\RecipeFinder;
use Megio\Http\Request\Request;
use Nette\Schema\Expect;
use Symfony\Component\HttpFoundation\Response;

class AddFormRequest extends Request
{
    public function __construct(
        protected readonly RecipeFinder $recipeFinder,
        protected readonly Builder      $builder,
    )
    {
    }
    
    public function schema(): array
    {
        $names = array_map(fn($r) => $r->name(), $this->recipeFinder->load()->getAll());
        
        return [
            'recipe' => Expect::anyOf(...$names)->required()
        ];
    }
    
    public function process(array $data): Response
    {
        $recipe = $this->recipeFinder->findByName($data['recipe']);
        
        if ($recipe === null) {
            return $this->error(["Collection '{$data['recipe']}' not found"]);
        }
        
        $builder = $recipe->create($this->builder->create($recipe, BuilderEventName::CREATE))->build();
        
        if ($builder->countFields() === 0) {
            return $this->error(["Collection '{$data['recipe']}' has no creatable fields"]);
        }
        
        return $this->json(['form' => $builder->toArray()]);
    }
}