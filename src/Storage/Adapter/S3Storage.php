<?php
declare(strict_types=1);

namespace Megio\Storage\Adapter;

use Aws\S3\Exception\S3Exception;
use Aws\S3\ObjectUploader;
use Aws\S3\S3Client;
use Exception;
use Megio\Helper\EnvConvertor;
use Megio\Storage\StorageAdapter;
use Nette\Utils\Strings;
use SplFileInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use function file_put_contents;
use function is_string;
use function ltrim;
use function parse_url;
use function pathinfo;
use function rtrim;

use const FILE_APPEND;
use const PATHINFO_FILENAME;
use const PHP_EOL;
use const PHP_URL_PATH;

class S3Storage implements StorageAdapter
{
    private S3Client $client;

    public function __construct(?S3Client $client = null)
    {
        // For mocking in tests
        if ($client !== null) {
            $this->client = $client;
            return;
        }

        $config = [
            'version' => 'latest',
            'endpoint' => EnvConvertor::toString($_ENV['S3_ENDPOINT']),
            'region' => EnvConvertor::toString($_ENV['S3_REGION']),
            'credentials' => [
                'key' => EnvConvertor::toString($_ENV['S3_KEY']),
                'secret' => EnvConvertor::toString($_ENV['S3_SECRET']),
            ],
        ];

        // MinIO requires path-style endpoint (optional, default false)
        $usePathStyle = $_ENV['S3_USE_PATH_STYLE_ENDPOINT'] ?? '';
        if ($usePathStyle !== '') {
            $config['use_path_style_endpoint'] = EnvConvertor::toBool($usePathStyle);
        }

        $this->client = new S3Client($config);
    }

    public function getS3Client(): S3Client
    {
        return $this->client;
    }

    public function upload(
        UploadedFile $file,
        string $destination,
        ?string $name = null,
        bool $publish = true,
    ): SplFileInfo {
        $ext = $file->getClientOriginalExtension();
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $fileName = ($name ?? $originalName) . ".{$ext}";
        $destination = $this->applySubfolder($destination);
        $destination = Strings::replace("{$destination}/{$fileName}", '~\/+~', '/');

        $uploader = new ObjectUploader(
            $this->client,
            EnvConvertor::toString($_ENV['S3_BUCKET']),
            $destination,
            $file->getContent(),
            $publish ? 'public-read' : 'private',
            [
                'params' => [
                    'ContentType' => $ext === 'svg' ? 'image/svg+xml' : $file->getClientMimeType(),
                ],
            ],
        );

        $uploader->upload();

        return new SplFileInfo($destination);
    }

    public function get(string $destination): ?string
    {
        $destination = $this->applySubfolder($destination);
        $destination = Strings::replace($destination, '~\/+~', '/');

        try {
            $res = $this->client->getObject([
                'Bucket' => EnvConvertor::toString($_ENV['S3_BUCKET']),
                'Key' => $destination,
            ]);

            $url = $res->get('@metadata')['effectiveUri'];

            return $this->applyCdnUrl($url);
        } catch (S3Exception $exception) {
            return $this->handleS3Exception($exception);
        }
    }

    public function put(
        string $destination,
        string $newLine,
    ): void {
        $destination = $this->applySubfolder($destination);
        $destination = Strings::replace($destination, '~\/+~', '/');

        $this->client->registerStreamWrapperV2();
        if (@file_put_contents(
            "s3://{$_ENV['S3_BUCKET']}/{$destination}",
            $newLine . PHP_EOL,
            FILE_APPEND, /*| LOCK_EX*/
        ) === false) {
            throw new Exception("Cannot write stream into AWS S3 file '{$destination}'");
        }
    }

    public function delete(SplFileInfo $file): void
    {
        $destination = $this->applySubfolder($file->getPathname());
        $destination = Strings::replace($destination, '~\/+~', '/');

        $prefix = $this->applySubfolder($file->getPath() . '/');
        $baseName = $file->getBasename('.' . $file->getExtension());
        $regex = "/{$baseName}--thumb(.*){$file->getExtension()}/";

        $bucket = EnvConvertor::toString($_ENV['S3_BUCKET']);
        $this->client->deleteMatchingObjectsAsync($bucket, $prefix, $regex);
        $this->client->deleteMatchingObjectsAsync($bucket, $destination)->wait();
    }

    public function deleteFolder(string $destination): void
    {
        $destination = $this->applySubfolder($destination);
        $destination = Strings::replace($destination, '~\/+~', '/');
        $this->client->deleteMatchingObjects(EnvConvertor::toString($_ENV['S3_BUCKET']), $destination);
    }

    /**
     * @return array<int, SplFileInfo>
     */
    public function list(string $destination): array
    {
        $destination = $this->applySubfolder($destination);
        $destination = Strings::replace($destination, '~\/+~', '/');

        $results = $this->client->getPaginator('ListObjects', [
            'Bucket' => EnvConvertor::toString($_ENV['S3_BUCKET']),
            'Prefix' => $destination,
        ]);

        $files = [];
        foreach ($results as $result) {
            if ($result['Contents'] !== null) {
                foreach ($result['Contents'] as $object) {
                    $files[] = new SplFileInfo($object['Key']);
                }
            }
        }

        return $files;
    }

    private function applySubfolder(string $destination): string
    {
        $subfolder = $_ENV['S3_SUBFOLDER'] ?? '';

        if ($subfolder !== '') {
            return rtrim($subfolder, '/') . '/' . ltrim($destination, '/');
        }

        return $destination;
    }

    /**
     * Replaces the domain with CDN URL if configured.
     * Works for both virtual-hosted (bucket.s3.region.amazonaws.com) and path-style (endpoint/bucket).
     */
    private function applyCdnUrl(string $url): string
    {
        $cdnUrl = $_ENV['S3_CDN_URL'] ?? '';

        if (is_string($cdnUrl) === false || $cdnUrl === '') {
            return $url;
        }

        $path = parse_url($url, PHP_URL_PATH);

        if (is_string($path) === false) {
            return $url;
        }

        return rtrim($cdnUrl, '/') . $path;
    }

    /**
     * @throws S3Exception
     */
    private function handleS3Exception(S3Exception $exception): ?string
    {
        if ($exception->getStatusCode() === 404) {
            return null;
        }

        throw $exception;
    }
}
