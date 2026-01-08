<?php

declare(strict_types=1);

namespace Tests\Unit\Storage;

use Aws\CommandInterface;
use Aws\MockHandler;
use Aws\Result;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use GuzzleHttp\Psr7\Response;
use Megio\Storage\Adapter\S3Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class S3StorageTest extends TestCase
{
    private const string TEST_BUCKET = 'test-bucket';

    public function testGetReturnsUrlFromS3Response(): void
    {
        $expectedUrl = 'https://bucket.s3.amazonaws.com/path/to/file.txt';

        $mock = new MockHandler([
            new Result([
                '@metadata' => ['effectiveUri' => $expectedUrl],
            ]),
        ]);

        $client = $this->createS3ClientWithMock($mock);
        $storage = new S3Storage($client);
        $url = $storage->get('path/to/file.txt');

        $this->assertSame($expectedUrl, $url);
    }

    public function testGetReturnsNullFor404Exception(): void
    {
        $mock = new MockHandler([
            static function (CommandInterface $cmd) {
                return new S3Exception(
                    'Not Found',
                    $cmd,
                    ['code' => 'NoSuchKey', 'response' => new Response(404)],
                );
            },
        ]);

        $client = $this->createS3ClientWithMock($mock);
        $storage = new S3Storage($client);
        $url = $storage->get('non-existent.txt');

        $this->assertNull($url);
    }

    public function testGetRethrowsNon404Exception(): void
    {
        $mock = new MockHandler([
            static function (CommandInterface $cmd) {
                return new S3Exception(
                    'Internal Server Error',
                    $cmd,
                    ['code' => 'InternalError', 'response' => new Response(500)],
                );
            },
        ]);

        $client = $this->createS3ClientWithMock($mock);
        $storage = new S3Storage($client);

        $thrownException = null;

        try {
            $storage->get('file.txt');
        } catch (S3Exception $e) {
            $thrownException = $e;
        }

        $this->assertNotNull($thrownException);
        $this->assertSame(500, $thrownException->getStatusCode());
    }

    /**
     * @param array{subfolder: string, destination: string, expectedKey: string} $data
     */
    #[DataProvider('subfolderProvider')]
    public function testApplySubfolder(array $data): void
    {
        $_ENV['S3_SUBFOLDER'] = $data['subfolder'];

        $capturedKey = null;
        $mock = new MockHandler([
            static function (CommandInterface $cmd) use (&$capturedKey) {
                $capturedKey = $cmd['Key'];

                return new Result([
                    '@metadata' => ['effectiveUri' => 'https://bucket.s3.amazonaws.com/' . $cmd['Key']],
                ]);
            },
        ]);

        $client = $this->createS3ClientWithMock($mock);
        $storage = new S3Storage($client);
        $storage->get($data['destination']);

        $this->assertSame($data['expectedKey'], $capturedKey);
    }

    /**
     * @return array<string, array{array{subfolder: string, destination: string, expectedKey: string}}>
     */
    public static function subfolderProvider(): array
    {
        return [
            'no subfolder' => [
                ['subfolder' => '', 'destination' => 'path/file.txt', 'expectedKey' => 'path/file.txt'],
            ],
            'with subfolder' => [
                ['subfolder' => 'tenant-123', 'destination' => 'path/file.txt', 'expectedKey' => 'tenant-123/path/file.txt'],
            ],
            'subfolder with trailing slash' => [
                ['subfolder' => 'tenant-123/', 'destination' => 'path/file.txt', 'expectedKey' => 'tenant-123/path/file.txt'],
            ],
            'destination with leading slash' => [
                ['subfolder' => 'tenant-123', 'destination' => '/path/file.txt', 'expectedKey' => 'tenant-123/path/file.txt'],
            ],
        ];
    }

    /**
     * @param array{cdnUrl: string, effectiveUri: string, expectedUrl: string} $data
     */
    #[DataProvider('cdnUrlProvider')]
    public function testApplyCdnUrl(array $data): void
    {
        $_ENV['S3_CDN_URL'] = $data['cdnUrl'];

        $mock = new MockHandler([
            new Result([
                '@metadata' => ['effectiveUri' => $data['effectiveUri']],
            ]),
        ]);

        $client = $this->createS3ClientWithMock($mock);
        $storage = new S3Storage($client);
        $url = $storage->get('file.txt');

        $this->assertSame($data['expectedUrl'], $url);
    }

    /**
     * @return array<string, array{array{cdnUrl: string, effectiveUri: string, expectedUrl: string}}>
     */
    public static function cdnUrlProvider(): array
    {
        return [
            'no CDN URL' => [
                [
                    'cdnUrl' => '',
                    'effectiveUri' => 'https://bucket.s3.amazonaws.com/path/file.txt',
                    'expectedUrl' => 'https://bucket.s3.amazonaws.com/path/file.txt',
                ],
            ],
            'with CDN URL (virtual-hosted)' => [
                [
                    'cdnUrl' => 'https://cdn.example.com',
                    'effectiveUri' => 'https://bucket.s3.amazonaws.com/path/file.txt',
                    'expectedUrl' => 'https://cdn.example.com/path/file.txt',
                ],
            ],
            'with CDN URL (path-style MinIO)' => [
                [
                    'cdnUrl' => 'https://cdn.example.com',
                    'effectiveUri' => 'https://minio.local/bucket/path/file.txt',
                    'expectedUrl' => 'https://cdn.example.com/bucket/path/file.txt',
                ],
            ],
            'CDN URL with trailing slash' => [
                [
                    'cdnUrl' => 'https://cdn.example.com/',
                    'effectiveUri' => 'https://bucket.s3.amazonaws.com/path/file.txt',
                    'expectedUrl' => 'https://cdn.example.com/path/file.txt',
                ],
            ],
        ];
    }

    public function testCombinedSubfolderAndCdnUrl(): void
    {
        $_ENV['S3_SUBFOLDER'] = 'tenant-123';
        $_ENV['S3_CDN_URL'] = 'https://cdn.example.com';

        $capturedKey = null;
        $mock = new MockHandler([
            static function (CommandInterface $cmd) use (&$capturedKey) {
                $capturedKey = $cmd['Key'];

                return new Result([
                    '@metadata' => ['effectiveUri' => 'https://bucket.s3.amazonaws.com/' . $cmd['Key']],
                ]);
            },
        ]);

        $client = $this->createS3ClientWithMock($mock);
        $storage = new S3Storage($client);
        $url = $storage->get('public/file.txt');

        $this->assertSame('tenant-123/public/file.txt', $capturedKey);
        $this->assertSame('https://cdn.example.com/tenant-123/public/file.txt', $url);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $_ENV['S3_BUCKET'] = self::TEST_BUCKET;
        $_ENV['S3_SUBFOLDER'] = '';
        $_ENV['S3_CDN_URL'] = '';
    }

    protected function tearDown(): void
    {
        unset($_ENV['S3_BUCKET'], $_ENV['S3_SUBFOLDER'], $_ENV['S3_CDN_URL']);

        parent::tearDown();
    }

    private function createS3ClientWithMock(MockHandler $mock): S3Client
    {
        return new S3Client([
            'region' => 'us-east-1',
            'version' => 'latest',
            'credentials' => [
                'key' => 'test-key',
                'secret' => 'test-secret',
            ],
            'handler' => $mock,
            'retries' => 0,
        ]);
    }
}
