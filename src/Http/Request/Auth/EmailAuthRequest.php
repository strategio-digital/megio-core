<?php
declare(strict_types=1);

namespace Megio\Http\Request\Auth;

use Nette\Security\Passwords;
use Megio\Database\EntityFinder;
use Megio\Database\Entity\Auth\Token;
use Megio\Database\EntityManager;
use Megio\Database\Interface\IAuthenticable;
use Megio\Http\Request\Request;
use Megio\Security\JWT\ClaimsFormatter;
use Megio\Security\JWT\JWTResolver;
use Symfony\Component\HttpFoundation\Response;
use Nette\Schema\Expect;

class EmailAuthRequest extends Request
{
    const EXPIRATION_TIME = '4hours';
    
    public function __construct(
        private readonly EntityManager   $em,
        private readonly JWTResolver     $jwt,
        private readonly ClaimsFormatter $claims,
        private readonly EntityFinder    $entityFinder
    )
    {
    }
    
    public function schema(): array
    {
        $all = $this->entityFinder->findAll();
        $filtered = array_filter($all, fn($item) => is_subclass_of($item['className'], IAuthenticable::class));
        $tables = array_map(fn($class) => $class['table'], $filtered);
        
        return [
            'source' => Expect::anyOf(...$tables),
            'email' => Expect::email()->max(128)->required(),
            'password' => Expect::string()->min(6)->max(32)->required()
        ];
    }
    
    public function process(array $data): Response
    {
        $className = $this->entityFinder->getClassName($data['source']);
        
        if (!$className || !is_subclass_of($className, IAuthenticable::class)) {
            return $this->error(['Invalid source']);
        }
        
        $userRepo = $this->em->getRepository($className);
        
        /** @var IAuthenticable|null $user */
        $user = $userRepo->findOneBy(['email' => $data['email']]);
        
        if (!$user || !(new Passwords(PASSWORD_ARGON2ID))->verify($data['password'], $user->getPassword())) {
            return $this->error(['Invalid e-mail or password credentials'], 401);
        }
        
        $token = new Token();
        $token->setSource($data['source']);
        $token->setSourceId($user->getId());
        $this->em->persist($token);
        
        $time = array_key_exists('AUTH_EXPIRATION', $_ENV) ? $_ENV['AUTH_EXPIRATION'] : self::EXPIRATION_TIME;
        
        $expiration = (new \DateTime())->modify('+' . $time);
        $immutable = \DateTimeImmutable::createFromMutable($expiration);
        $claims = $this->claims->format($user, $token);
        $jwt = $this->jwt->createToken($immutable, $claims);
        
        $token->setExpiration($expiration);
        $token->setToken($jwt);
        $user->setLastLogin();
        
        $this->em->flush($token);
        $this->em->flush($user);
        
        return $this->json([
            'bearer_token' => $token->getToken(),
            ...$claims
        ]);
    }
}