<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;
use Nette\Schema\Processor;
use Nette\Schema\Schema;
use Nette\Schema\ValidationException;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class JsonRule extends BaseRule
{
    public function __construct(
        protected ?Schema $schema = null,
        protected ?string $message = null
    )
    {
        parent::__construct($message);
    }
    
    public function message(): string
    {
        return $this->message ?: "Field must be a valid JSON";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        
        if (is_array($value)) {
            try {
                Json::encode($value);
                if ($this->schema) {
                    $processor = new Processor();
                    $processor->process($this->schema, $value);
                }
                return true;
            } catch (JsonException | ValidationException $e) {
                if ($e instanceof ValidationException) {
                    $this->message = implode(', ', $e->getMessages());
                }
            }
        }
        
        return false;
    }
}