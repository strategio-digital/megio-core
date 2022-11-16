<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Security\JWT;

use Saas\Helper\Path;
use Saas\Security\SSL\KeyPair;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Validation\Constraint;

class Jwt
{
    const ISSUER = 'strategio-fw';
    
    const PERMIT_FOR = 'strategio-fw-apps';
    
    private Configuration $config;
    
    public function __construct()
    {
        $filePath = Path::tempDir() . '/jwt/jwt-RSA.key';
        
        if (!is_file($filePath)) {
            $keyPair = KeyPair::generate();
            $keyPair->saveTo($filePath);
        } else {
            $keyPair = KeyPair::fromFile($filePath);
        }
        
        $this->config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($keyPair->getPrivateKey())
        );
    }
    
    public function parseToken(string $token): Token
    {
        return $this->config->parser()->parse($token);
    }
    
    public function isTrustedToken(string $token): bool
    {
        $this->config->setValidationConstraints(
            new Constraint\SignedWith($this->config->signer(), $this->config->verificationKey()),
            new Constraint\IssuedBy(self::ISSUER),
            new Constraint\PermittedFor(self::PERMIT_FOR),
            new Constraint\LooseValidAt(SystemClock::fromSystemTimezone())
        );
        
        $constraints = $this->config->validationConstraints();
        
        try {
            $token = $this->config->parser()->parse($token);
            return $this->config->validator()->validate($token, ...$constraints);
        } catch (InvalidTokenStructure|CannotDecodeContent) {
            return false;
        }
    }
    
    /**
     * @param \DateTimeImmutable $expirationAt
     * @param array<string, mixed> $claims
     * @return string
     */
    public function createToken(\DateTimeImmutable $expirationAt, array $claims = []): string
    {
        $createdAt = new \DateTimeImmutable();
        
        $builder = $this->config
            ->builder()
            ->issuedBy(self::ISSUER)
            ->permittedFor(self::PERMIT_FOR)
            ->issuedAt($createdAt)
            ->expiresAt($expirationAt);
        
        foreach ($claims as $name => $value) {
            $builder->withClaim($name, $value);
        }
        
        return $builder->getToken($this->config->signer(), $this->config->signingKey())->toString();
    }
}