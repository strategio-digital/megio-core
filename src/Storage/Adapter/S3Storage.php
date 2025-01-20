<?php
declare(strict_types=1);

namespace Megio\Storage\Adapter;

use Aws\S3\Exception\S3Exception;
use Aws\S3\ObjectUploader;
use Aws\S3\S3Client;
use Megio\Storage\StorageAdapter;
use Nette\Utils\Strings;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class S3Storage implements StorageAdapter
{
    private S3Client $client;
    
    public function __construct()
    {
        $this->client = new S3Client([
            'version' => 'latest',
            'endpoint' => $_ENV['S3_ENDPOINT'],
            'region' => $_ENV['S3_REGION'],
            'credentials' => [
                'key' => $_ENV['S3_KEY'],
                'secret' => $_ENV['S3_SECRET'],
            ]
        ]);
    }
    
    public function getS3Client(): S3Client
    {
        return $this->client;
    }
    
    public function upload(UploadedFile $file, string $destination, string $name = null, bool $publish = true): \SplFileInfo
    {
        $ext = $file->getClientOriginalExtension();
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        
        $fileName = ($name ?? $originalName). ".{$ext}";
        $destination = Strings::replace("{$destination}/{$fileName}", '~\/+~', '/');
        
        $uploader = new ObjectUploader(
            $this->client,
            $_ENV['S3_BUCKET'],
            $destination,
            $file->getContent(),
            $publish ? 'public-read' : 'private',
            ['params' => [
                'ContentType' => $ext === 'svg' ? 'image/svg+xml' : $file->getClientMimeType(),
            ]]
        );
        
        $uploader->upload();
        
        return new \SplFileInfo($destination);
    }
    
    public function get(string $destination): ?string
    {
        $destination = Strings::replace($destination, '~\/+~', '/');
        
        try {
            $res = $this->client->getObject(['Bucket' => $_ENV['S3_BUCKET'], 'Key' => $destination,]);
            return $res->get('@metadata')['effectiveUri'];
        } catch (S3Exception $exception) {
            if ($exception->getStatusCode() === 404) {
                return null;
            }
            throw $exception;
        }
    }
    
    public function put(string $destination, string $newLine): void
    {
        $destination = Strings::replace($destination, '~\/+~', '/');
    
        $this->client->registerStreamWrapperV2();
        if (!@file_put_contents("s3://{$_ENV['S3_BUCKET']}/{$destination}", $newLine . PHP_EOL, FILE_APPEND /*| LOCK_EX*/)) {
            throw new \Exception("Cannot write stream into AWS S3 file '{$destination}'");
        }
    }
    
    public function delete(\SplFileInfo $file): void
    {
        $destination = Strings::replace($file->getPathname(), '~\/+~', '/');
        
        $prefix = $file->getPath() . '/';
        $baseName = $file->getBasename('.' . $file->getExtension());
        $regex = "/{$baseName}--thumb(.*){$file->getExtension()}/";
        
        $this->client->deleteMatchingObjectsAsync($_ENV['S3_BUCKET'], $prefix, $regex);
        $this->client->deleteMatchingObjectsAsync($_ENV['S3_BUCKET'], $destination)->wait();
    }
    
    public function deleteFolder(string $destination): void
    {
        $destination = Strings::replace($destination, '~\/+~', '/');
        $this->client->deleteMatchingObjects($_ENV['S3_BUCKET'], $destination);
    }
    
    /**
     * @param string $destination
     * @return array<int, \SplFileInfo>
     */
    public function list(string $destination): array
    {
        $destination = Strings::replace($destination, '~\/+~', '/');
        
        $results = $this->client->getPaginator('ListObjects', [
            'Bucket' => $_ENV['S3_BUCKET'],
            'Prefix' => $destination
        ]);
        
        $files = [];
        foreach ($results as $result) {
            if ($result['Contents'] !== null) {
                foreach ($result['Contents'] as $object) {
                    $files[] = new \SplFileInfo($object['Key']);
                }
            }
        }
        
        return $files;
    }
}