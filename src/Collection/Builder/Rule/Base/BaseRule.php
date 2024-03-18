<?php
declare(strict_types=1);

namespace Megio\Collection\Builder\Rule\Base;

use Megio\Collection\Builder\Field\Base\IField;

abstract class BaseRule implements IRule
{
    protected IField $field;
    
    /** @var IField[] */
    protected array $relatedFields = [];
    
    /** @var IRule[] */
    protected array $relatedRules = [];
    
    public function __construct(protected string|null $message = null)
    {
    }
    
    public function setField(IField $field): void
    {
        $this->field = $field;
    }
    
    public function setRelatedFields(array $fields): void
    {
        $this->relatedFields = $fields;
    }
    
    public function setRelatedRules(array $rules): void
    {
        $this->relatedRules = $rules;
    }
    
    /**
     * Structured description for usage in front-end form
     * @return array{name: string, message: string, params: array<string,mixed>}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name(),
            'message' => $this->message(),
            'params' => [],
        ];
    }
}