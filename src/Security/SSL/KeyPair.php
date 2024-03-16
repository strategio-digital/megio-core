<?php
declare(strict_types=1);

namespace Megio\Security\SSL;

use Nette\Utils\FileSystem;

class KeyPair
{
    private function __construct(protected string $privateKey, protected string $publicKey)
    {
    }
    
    public static function generate(int $bits = 4096, int $keyType = OPENSSL_KEYTYPE_RSA): KeyPair
    {
        /** @var \OpenSSLAsymmetricKey $keys */
        $keys = openssl_pkey_new([
            'private_key_bits' => $bits,
            'private_key_type' => $keyType,
        ]);
        
        /** @var array{key:string} $publicKeyPem */
        $publicKeyPem = openssl_pkey_get_details($keys);
        
        openssl_pkey_export($keys, $privateKeyPem);
        
        return new self($privateKeyPem, $publicKeyPem['key']);
    }
    
    public static function fromFile(string $filePath): KeyPair
    {
        $privateKey = FileSystem::read($filePath);
        $publicKey = FileSystem::read($filePath . '.pub');
        return new self($privateKey, $publicKey);
    }
    
    /**
     * @return non-empty-string
     * @throws \Exception
     */
    public function getPublicKey(): string
    {
        if ($this->publicKey) {
            return $this->publicKey;
        }
        
        throw new \Exception('Public key is empty-string!');
    }
    
    /**
     * @return non-empty-string
     * @throws \Exception
     */
    public function getPrivateKey(): string
    {
        if ($this->privateKey) {
            return $this->privateKey;
        }
        
        throw new \Exception('Private key is empty-string!');
    }
    
    public function saveTo(string $filePath): void
    {
        FileSystem::write($filePath, $this->privateKey);
        FileSystem::write($filePath . '.pub', $this->publicKey);
    }
}