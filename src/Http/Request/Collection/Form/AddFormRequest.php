<?php
declare(strict_types=1);

namespace Megio\Http\Request\Collection\Form;

use Megio\Collection\Exception\CollectionException;
use Megio\Collection\WriteBuilder\WriteBuilder;
use Megio\Collection\WriteBuilder\WriteBuilderEvent;
use Megio\Collection\RecipeFinder;
use Megio\Http\Request\Request;
use Nette\Schema\Expect;
use Symfony\Component\HttpFoundation\Response;

class AddFormRequest extends Request
{
    public function __construct(
        protected readonly RecipeFinder $recipeFinder,
        protected readonly WriteBuilder $builder,
    )
    {
    }
    
    public function schema(): array
    {
        $recipeKeys = array_map(fn($r) => $r->key(), $this->recipeFinder->load()->getAll());
        
        return [
            'recipe' => Expect::anyOf(...$recipeKeys)->required()
        ];
    }
    
    public function process(array $data): Response
    {
        $recipe = $this->recipeFinder->findByKey($data['recipe']);
        
        if ($recipe === null) {
            return $this->error(["Collection '{$data['recipe']}' not found"]);
        }
        
        try {
            $builder = $recipe->create($this->builder->create($recipe, WriteBuilderEvent::CREATE), $this->request)->build();
        } catch (CollectionException $e) {
            return $this->error([$e->getMessage()]);
        }
        
        if ($builder->countFields() === 0) {
            return $this->error(["Collection '{$data['recipe']}' has no creatable fields"]);
        }
        
        return $this->json(['form' => $builder->toArray()]);
    }
}