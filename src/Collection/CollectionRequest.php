<?php
declare(strict_types=1);

namespace Megio\Collection;

use Symfony\Component\HttpFoundation\Request;

class CollectionRequest
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param bool $isFormRendering
     * @param array<int|string, mixed> $requestData
     * @param string|null $rowId
     * @param array<string, string|int|float|bool|null> $rowValues
     */
    public function __construct(
        protected Request &$request,
        protected bool    $isFormRendering,
        protected mixed   $requestData,
        protected ?string $rowId = null,
        protected array   $rowValues = [],
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
    
    /**
     * @return array<int|string, mixed>
     */
    public function getRequestData(): mixed
    {
        return $this->requestData;
    }
    
    public function getCustomData(): mixed
    {
        if (array_key_exists('custom_data', $this->requestData)) {
            return $this->requestData['custom_data'];
        }
        
        return [];
    }
    
    public function getSearchText(): ?string
    {
        if (array_key_exists('search', $this->requestData) && array_key_exists('text', $this->requestData['search'])) {
            return $this->requestData['search']['text'];
        }
        
        return null;
    }
}