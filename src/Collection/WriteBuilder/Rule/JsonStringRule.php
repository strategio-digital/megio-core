<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;
use Nette\Schema\Processor;
use Nette\Schema\Schema;
use Nette\Schema\ValidationException;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class JsonStringRule extends BaseRule
{
    public function __construct(
        protected ?Schema $schema = null,
        protected ?string $message = null,
    ) {
        parent::__construct($message);
    }

    public function message(): string
    {
        return $this->message ?: "Field must be a valid JSON string";
    }

    /**
     * Return true if validation is passed
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();

        if (!is_string($value)) {
            return false;
        }

        try {
            $json = Json::decode($value);
            if ($this->schema) {
                $processor = new Processor();
                $processor->process($this->schema, $json);
            }
            return true;
        } catch (JsonException|ValidationException $e) {
            if ($e instanceof ValidationException) {
                $this->message = implode(', ', $e->getMessages());
            }
        }

        return false;
    }
}
