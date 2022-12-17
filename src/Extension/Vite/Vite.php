<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Extension\Vite;

use Saas\Helper\Path;

class Vite
{
    /**
     * @var array<string, mixed>|null
     */
    protected array|null $manifest = null;
    
    public function resolveSource(string $source): string
    {
        if (file_exists(Path::tempDir() . '/vite.hot')) {
            return file_get_contents(Path::tempDir() . '/vite.hot') . '/' . $source;
        }
        
        $entry = $this->getManifest()[$source];
        return '/public/' . $entry['file'];
    }
    
    public function resolveEntrypoint(string $entryPoint): string
    {
        if (file_exists(Path::tempDir() . '/vite.hot')) {
            $source = file_get_contents(Path::tempDir() . '/vite.hot') . '/' . $entryPoint;
            return $this->sourceToHtmlTag($source);
        }
        
        $entry = $this->getManifest()[$entryPoint];
        
        $result = array_merge(
            [$this->sourceToHtmlTag('/public/' . $entry['file'])],
            array_key_exists('css', $entry) ? array_map(fn($source) => $this->sourceToHtmlTag('/public/' . $source), $entry['css']) : [],
            array_key_exists('js', $entry) ? array_map(fn($source) => $this->sourceToHtmlTag('/public/' . $source), $entry['js']) : []
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
            if (!file_exists(Path::publicDir() . '/manifest.json')) {
                throw new \Exception("Vite manifest file not found, please execute 'yarn build' or 'yarn dev' command.");
            }

            $content = file_get_contents(Path::publicDir() . '/manifest.json');
            
            if (!$content) {
                throw new \Exception("Vite manifest file has wrong format.");
            }
            
            $this->manifest = json_decode($content, true);;
        }
        
        return $this->manifest;
    }
    
    protected function sourceToHtmlTag(string $source): string
    {
        $extension = pathinfo($source, PATHINFO_EXTENSION);
        
        if ($extension === 'css') {
            return '<link rel="stylesheet" href="' . $source . '">';
        }
        
        if ($extension === 'js' || $extension === 'ts') {
            return '<script type="module" src="' . $source . '"></script>';
        }
        
        throw new \Exception("Unknown asset file extension '{$extension}'.");
    }
}