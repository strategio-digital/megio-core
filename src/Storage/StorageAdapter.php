<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Storage;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface StorageAdapter
{
    public function upload(UploadedFile $file, string $destination, bool $publish = true): \SplFileInfo;
    
    public function get(string $destination): ?string;
    
    public function put(string $destination, string $newLine): void;
    
    public function delete(\SplFileInfo $file): void;
    
    public function deleteFolder(string $destination): void;
    
    /**
     * @param string $destination
     * @return array<int, \SplFileInfo>
     */
    public function list(string $destination): array;
}