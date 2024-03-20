<?php
declare(strict_types=1);

namespace Megio\Collection\FieldBuilder\Rule;

use Megio\Collection\FieldBuilder\Rule\Base\BaseRule;

class VideoLinkRule extends BaseRule
{
    public function __construct(
        protected string|null $message = null,
        protected bool        $normalize = true
    )
    {
        parent::__construct($message);
    }
    
    public function name(): string
    {
        return 'videoLink';
    }
    
    public function message(): string
    {
        return $this->message ?: "Field '{$this->field->getName()}' is not valid youtube or vimeo video link.";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        $nullable = array_filter($this->relatedRules, fn($rule) => $rule->name() === 'nullable');
        
        if (count($nullable) !== 0 && $value === null) {
            return true;
        }
        
        if (!is_string($value)) {
            return false;
        }
        
        // Recognize https://www.youtube.com/watch?v=3q4NsQN0gLw&ab_channel=SPORTSNET
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $value, $matches)) {
            if ($this->normalize) {
                $this->field->setValue('https://www.youtube-nocookie.com/embed/' . $matches[1]);
            }
            return true;
        }
        
        // Recognize https://youtu.be/kDFpGxFBZ4Q?si=1cgZUigAqqsw4XfN
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $value, $matches)) {
            if ($this->normalize) {
                $this->field->setValue('https://www.youtube-nocookie.com/embed/' . $matches[1]);
            }
            return true;
        }
        
        // Recognize Vimeo URL
        if (preg_match('/vimeo\.com\/([0-9]+)/', $value, $matches)) {
            if ($this->normalize) {
                $this->field->setValue('https://player.vimeo.com/video/' . $matches[1]);
            }
            return true;
        }
        
        return false;
    }
}