<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Extension\Latte\Helper;

use Megio\Helper\Path;

class Vite
{
    /**
     * @var array<string, mixed>|null
     */
    protected array|null $manifest = null;
    
    protected bool $isFirstEntry = false;
    
    public function resolveSource(string $source): string
    {
        if (file_exists(Path::tempDir() . '/vite.hot')) {
            return file_get_contents(Path::tempDir() . '/vite.hot') . '/' . $source;
        }
        
        $entry = $this->getManifest()[$source];
        return '/temp/' . $entry['file'];
    }
    
    public function resolveEntrypoint(string $entryPoint): string
    {
        if (file_exists(Path::tempDir() . '/vite.hot')) {
            $domain = file_get_contents(Path::tempDir() . '/vite.hot') . '/';
            
            $html = [];
            
            if (!$this->isFirstEntry) {
                $html[] = $this->sourceToHtmlTag($domain . '@vite/client');
                $this->isFirstEntry = true;
            }
            
            $html[] = $this->sourceToHtmlTag($domain . $entryPoint);
            
            return implode('', $html);
        }
        
        $entry = $this->getManifest()[$entryPoint];
        
        $result = array_merge(
            [$this->sourceToHtmlTag('/temp/' . $entry['file'])],
            array_key_exists('css', $entry) ? array_map(fn($source) => $this->sourceToHtmlTag('/temp/' . $source), $entry['css']) : [],
            array_key_exists('js', $entry) ? array_map(fn($source) => $this->sourceToHtmlTag('/temp/' . $source), $entry['js']) : []
        );
        
        return implode(PHP_EOL, $result);
    }
    
    /**
     * @return array<string, mixed>
     * @throws \Exception
     */
    protected function getManifest(): array
    {
        if (!$this->manifest) {
            if (!file_exists(Path::wwwTempDir() . '/manifest.json')) {
                throw new \Exception("Vite manifest file not found, please execute 'yarn build' or 'yarn dev' command.");
            }
            
            $content = file_get_contents(Path::wwwTempDir() . '/manifest.json');
            
            if (!$content) {
                throw new \Exception("Vite manifest file has wrong format.");
            }
            
            $this->manifest = json_decode($content, true);
        }
        
        return $this->manifest;
    }
    
    protected function sourceToHtmlTag(string $source): string
    {
        $extension = pathinfo($source, PATHINFO_EXTENSION);
        
        if ($extension === 'css') {
            return '<link rel="stylesheet" href="' . $source . '">';
        }
        
        return '<script type="module" src="' . $source . '"></script>';
    }
}