<?php
declare(strict_types=1);

namespace Megio\Event\Collection;

use Megio\Collection\ICollectionRecipe;
use Megio\Collection\RecipeRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class OnFormStartEvent extends Event
{
    protected ?Response $response = null;
    
    public function __construct(
        protected bool              $creatingForm,
        protected mixed             $data,
        protected ICollectionRecipe $recipe,
        protected Request           $request,
    )
    {
    }
    
    /**
     * @return ICollectionRecipe
     */
    public function getRecipe(): ICollectionRecipe
    {
        return $this->recipe;
    }
    
    public function isCreatingForm(): bool
    {
        return $this->creatingForm;
    }
    
    public function getData(): mixed
    {
        return $this->data;
    }
    
    /**
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }
    
    public function getRequest(): Request
    {
        return $this->request;
    }
    
    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}