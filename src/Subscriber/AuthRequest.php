<?php
declare(strict_types=1);

namespace Megio\Subscriber;

use DateTime;
use Megio\Database\Entity\Admin;
use Megio\Database\EntityFinder;
use Megio\Database\EntityManager;
use Megio\Database\Interface\IAuthenticable;
use Megio\Security\Auth\AuthUser;
use Megio\Security\JWT\JWTResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class AuthRequest implements EventSubscriberInterface
{
    public const string HEADER_NAME = 'X-Auth-Reject-Reason';

    public const string INVALID_CREDENTIALS = 'invalid_credentials';

    public const string INVALID_PERMISSIONS = 'invalid_permissions';

    protected RequestEvent $event;

    protected Request $request;

    public function __construct(
        protected EntityFinder    $entityFinder,
        protected JWTResolver     $jwt,
        protected EntityManager   $em,
        protected RouteCollection $routes,
        protected AuthUser        $authUser,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest'],
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        $this->event = $event;
        $this->request = $event->getRequest();

        $routeName = $this->request->attributes->get('_route');

        if ($routeName === null) {
            return;
        }

        /** @var Route $currentRoute */
        $currentRoute = $this->routes->get($routeName);

        if ($currentRoute->getOption('auth') === false) {
            return;
        }

        $authHeader = $this->request->headers->get('Authorization');

        if (is_string($authHeader) === false) {
            $this->sendError('Invalid or empty Authorization header', 401, [
                self::HEADER_NAME => self::INVALID_CREDENTIALS,
            ]);
            return;
        }

        $bearer = trim(str_replace('Bearer', '', $authHeader));

        if ($bearer === '') {
            $this->sendError('Empty Bearer token', 401, [
                self::HEADER_NAME => self::INVALID_CREDENTIALS,
            ]);
            return;
        }

        if ($this->jwt->isTrustedToken($bearer) === false) {
            $this->sendError('Invalid or expired token', 401, [
                self::HEADER_NAME => self::INVALID_CREDENTIALS,
            ]);
            return;
        }

        $jwt = $this->jwt->parseToken($bearer);

        $claims = $jwt->claims();

        $tokenId = $claims->get('bearer_token_id');

        if ($tokenId === null) {
            $this->sendError('Missing bearer_token_id in JWT token claims', 401, [
                self::HEADER_NAME => self::INVALID_CREDENTIALS,
            ]);
            return;
        }

        $token = $this->em->getAuthTokenRepo()->findOneBy(['id' => $tokenId]);

        if ($token === null) {
            $this->sendError('Unknown JWT token, probably it was revoked', 401, [
                self::HEADER_NAME => self::INVALID_CREDENTIALS,
            ]);
            return;
        }

        if ($jwt->isExpired(new DateTime())) {
            $this->em->remove($token);
            $this->em->flush();
            $this->sendError('JWT token expired', 401, [
                self::HEADER_NAME => self::INVALID_CREDENTIALS,
            ]);
            return;
        }

        $className = $this->entityFinder->getClassName($token->getSource());

        if ($className === null || is_subclass_of($className, IAuthenticable::class) === false) {
            $this->sendError("For source {$token->getSource()} does not exists IAuthenticable entity");
            return;
        }

        $userRepo = $this->em->getRepository($className);

        $qb = $userRepo->createQueryBuilder('user')
            ->select('user')
            ->andWhere('user.id = :source_id')
            ->setParameter('source_id', $token->getSourceId());

        if ($className !== Admin::class) {
            $qb
                ->addSelect('role')
                ->addSelect('resource')
                ->leftJoin('user.roles', 'role')
                ->leftJoin('role.resources', 'resource');
        }

        $user = $qb->getQuery()->getOneOrNullResult();

        if ($user instanceof IAuthenticable === false) {
            $this->sendError("User does not exist in '{$token->getSource()}' source", 401, [
                self::HEADER_NAME => self::INVALID_CREDENTIALS,
            ]);
            return;
        }

        $this->authUser->setAuthUser($user);

        // Strictly watch resources, if AUTH_STRICT_RESOURCES is not set to false
        $watch = !(array_key_exists('AUTH_STRICT_RESOURCES', $_ENV) && $_ENV['AUTH_STRICT_RESOURCES'] === 'false');

        if ($watch === true) {
            $claimsResources = $claims->get('user')['resources'];
            $authResources = $this->authUser->getResources();

            sort($claimsResources);
            sort($authResources);

            $requestResources = implode('|', $claimsResources);
            $userResources = implode('|', $authResources);

            if ($userResources !== $requestResources) {
                $this->sendError('User permissions have been changed', 401, [
                    self::HEADER_NAME => self::INVALID_PERMISSIONS,
                ]);
            }
        }
    }

    /**
     * @param array<string, string> $headers
     */
    public function sendError(string $error, int $status = 401, array $headers = []): void
    {
        $this->event->setResponse(new JsonResponse(['errors' => [$error]], $status, $headers));
        $this->event->stopPropagation();
    }
}
