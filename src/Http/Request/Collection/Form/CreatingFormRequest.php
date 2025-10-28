<?php
declare(strict_types=1);

namespace Megio\Http\Request\Collection\Form;

use Megio\Collection\CollectionRequest;
use Megio\Collection\Exception\CollectionException;
use Megio\Collection\RecipeFinder;
use Megio\Collection\WriteBuilder\WriteBuilder;
use Megio\Collection\WriteBuilder\WriteBuilderEvent;
use Megio\Event\Collection\Events;
use Megio\Event\Collection\OnFormStartEvent;
use Megio\Http\Request\Request;
use Nette\Schema\Expect;
use Symfony\Component\HttpFoundation\Response;

class CreatingFormRequest extends Request
{
    public function __construct(
        protected readonly RecipeFinder $recipeFinder,
        protected readonly WriteBuilder $builder,
    ) {}

    public function schema(array $data): array
    {
        $recipeKeys = array_map(fn(
            $r,
        ) => $r->key(), $this->recipeFinder->load()->getAll());

        return [
            'recipeKey' => Expect::anyOf(...$recipeKeys)->required(),
            'custom_data' => Expect::arrayOf('int|float|string|bool|null|array', 'string')->nullable()->default([]),
        ];
    }

    public function process(array $data): Response
    {
        $recipe = $this->recipeFinder->findByKey($data['recipeKey']);

        if ($recipe === null) {
            return $this->error(["Collection '{$data['recipeKey']}' not found"]);
        }

        $event = new OnFormStartEvent(true, $data, $recipe, $this->request);
        $dispatcher = $this->dispatcher->dispatch($event, Events::ON_FORM_START->value);

        if ($dispatcher->getResponse()) {
            return $dispatcher->getResponse();
        }

        $collectionRequest = new CollectionRequest($this->request, true, $data, null, []);

        try {
            $defaultBuilder = $this->builder->create($recipe, WriteBuilderEvent::CREATE);
            $builder = $recipe->create($defaultBuilder, $collectionRequest)->build();
        } catch (CollectionException $e) {
            return $this->error([$e->getMessage()]);
        }

        if ($builder->countFields() === 0) {
            return $this->error(["Collection '{$data['recipe']}' has no creatable fields"]);
        }

        return $this->json([
            'recipe' => [
                'key' => $recipe->key(),
                'name' => $recipe->name(),
            ],
            'form' => $builder->toArray(),
        ]);
    }
}
