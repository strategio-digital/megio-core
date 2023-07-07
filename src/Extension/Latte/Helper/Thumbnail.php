<?php
/**
 * Copyright (c) 2023 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Saas\Extension\Helper;

class Thumbnail
{
    protected \SplFileInfo $splFileInfo;
    
    protected string $suffix;
    
    protected string $thumbFilePath;
    
    public function __construct(protected string $path, protected ?int $width, protected ?int $height, protected string $method = 'EXACT', protected int $quality = 80)
    {
        $this->method = mb_strtoupper($this->method);
        
        $this->splFileInfo = new \SplFileInfo($path);
        $this->suffix = '--thumb[' . $this->method . '-' . $quality . '-' . $width . 'x' . $height . ']';
        
        $this->thumbFilePath = $this->splFileInfo->getPath() . '/'
            . $this->splFileInfo->getBasename('.' . $this->splFileInfo->getExtension()) . $this->suffix . '.'
            . $this->splFileInfo->getExtension();
    }
    
    public function getWidth(): ?int
    {
        return $this->width;
    }
    
    public function getHeight(): ?int
    {
        return $this->height;
    }
    
    public function getSrc(): string
    {
        return $this->thumbFilePath;
    }
    
    public function getSourceSrc(): string
    {
        return $this->path;
    }
}