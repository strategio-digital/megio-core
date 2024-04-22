<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\Exception\CollectionException;
use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class CallableRule extends BaseRule
{
    /**
     * @var array<int, callable> $callback
     */
    protected array $callback;
    
    public function __construct(
        callable          $callback,
        protected ?string $message = null,
    )
    {
        $this->callback = [$callback];
        parent::__construct(message: $message);
    }
    
    public function message(): string
    {
        return $this->message ?: "Field is not valid.";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     * @throws \Megio\Collection\Exception\CollectionException
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        
        $result = $this->callback[0]($value, $this);
        
        if ($result === true || $result === false) {
            return $result;
        } else {
            throw new CollectionException("CallableRule in field '{$this->field->getName()}' must return boolean");
        }
    }
}