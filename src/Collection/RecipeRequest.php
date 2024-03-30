<?php
declare(strict_types=1);

namespace Megio\Collection;

use Megio\Collection\WriteBuilder\Field\Base\UndefinedValue;
use Symfony\Component\HttpFoundation\Request;

class RecipeRequest
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param bool $isFormRendering
     * @param string|null $rowId
     * @param array<string, string|int|float|bool|null> $rowValues
     * @param mixed|UndefinedValue $customData
     */
    public function __construct(
        protected Request &$request,
        protected bool    $isFormRendering,
        protected ?string $rowId = null,
        protected array   $rowValues = [],
        protected mixed   $customData = new UndefinedValue(),
    )
    {
    }
    
    public function getHttpRequest(): Request
    {
        return $this->request;
    }
    
    public function isFormRendering(): bool
    {
        return $this->isFormRendering;
    }
    
    public function getRowId(): ?string
    {
        return $this->rowId;
    }
    
    public function getRowValues(): mixed
    {
        return $this->rowValues;
    }
    
    public function getCustomData(): mixed
    {
        return $this->customData;
    }
}