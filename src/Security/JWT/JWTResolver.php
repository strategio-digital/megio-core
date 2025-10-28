<?php
declare(strict_types=1);

namespace Megio\Security\JWT;

use DateTimeImmutable;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Validator;
use Megio\Helper\Path;
use Megio\Security\SSL\KeyPair;
use Psr\Clock\ClockInterface;

class JWTResolver
{
    public const string ISSUER = 'strategio.dev';

    public const string PERMIT_FOR = 'strategio-megio-apps';

    private Sha256 $signer;

    private InMemory $signingKey;

    private ClockInterface $clock;

    public function __construct()
    {
        $filePath = Path::tempDir() . '/jwt/jwt-RSA.key';

        if (!is_file($filePath)) {
            $keyPair = KeyPair::generate();
            $keyPair->saveTo($filePath);
        } else {
            $keyPair = KeyPair::fromFile($filePath);
        }

        $this->signer = new Sha256();
        $this->signingKey = InMemory::plainText($keyPair->getPrivateKey());
        $this->clock = new class implements ClockInterface {
            public function now(): DateTimeImmutable
            {
                return new DateTimeImmutable();
            }
        };
    }

    /**
     * @param non-empty-string $token
     */
    public function parseToken(string $token): Plain
    {
        $parser = new Parser(new JoseEncoder());
        $token = $parser->parse($token);
        assert($token instanceof Plain);

        return $token;
    }

    /**
     * @param non-empty-string $token
     */
    public function isTrustedToken(string $token): bool
    {
        $constraints = [
            new SignedWith($this->signer, $this->signingKey),
            new IssuedBy(self::ISSUER),
            new PermittedFor(self::PERMIT_FOR),
            new LooseValidAt($this->clock),
        ];

        try {
            $parsedToken = $this->parseToken($token);
            $validator = new Validator();
            return $validator->validate($parsedToken, ...$constraints);
        } catch (InvalidTokenStructure|CannotDecodeContent) {
            return false;
        }
    }

    /**
     * @param array<non-empty-string, mixed> $claims
     */
    public function createToken(
        DateTimeImmutable $expirationAt,
        array $claims = [],
    ): string {
        $createdAt = $this->clock->now();

        $builder = new Builder(new JoseEncoder(), ChainedFormatter::default());
        $builder = $builder
            ->issuedBy(self::ISSUER)
            ->permittedFor(self::PERMIT_FOR)
            ->issuedAt($createdAt)
            ->expiresAt($expirationAt);

        foreach ($claims as $name => $value) {
            $builder = $builder->withClaim($name, $value);
        }

        return $builder->getToken($this->signer, $this->signingKey)->toString();
    }
}
