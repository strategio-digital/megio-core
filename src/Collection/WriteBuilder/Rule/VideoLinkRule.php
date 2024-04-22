<?php
declare(strict_types=1);

namespace Megio\Collection\WriteBuilder\Rule;

use Megio\Collection\WriteBuilder\Rule\Base\BaseRule;

class VideoLinkRule extends BaseRule
{
    public function __construct(
        protected ?string $message = null,
        protected bool    $normalize = true
    )
    {
        parent::__construct(message: $message);
    }
    
    public function message(): string
    {
        return $this->message ?: "Field is not valid YouTube or Vimeo video link";
    }
    
    /**
     * Return true if validation is passed
     * @return bool
     */
    public function validate(): bool
    {
        $value = $this->field->getValue();
        
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