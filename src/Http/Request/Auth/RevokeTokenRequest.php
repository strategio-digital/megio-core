<?php
declare(strict_types=1);

namespace Megio\Http\Request\Auth;

use Megio\Database\EntityFinder;
use Megio\Database\EntityManager;
use Megio\Database\Interface\IAuthenticable;
use Megio\Http\Request\AbstractRequest;
use Nette\Schema\Expect;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RevokeTokenRequest extends AbstractRequest
{
    public function __construct(
        private readonly EntityManager $em,
        private readonly EntityFinder $entityFinder,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function schema(): array
    {
        $all = $this->entityFinder->findAll();
        $filtered = array_filter($all, fn(
            $item,
        ) => is_subclass_of($item['className'], IAuthenticable::class));
        $tables = array_map(fn(
            $class,
        ) => $class['table'], $filtered);

        return [
            'source_ids' => Expect::arrayOf('string')->required(),
            'source' => Expect::anyOf(...$tables),
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function processValidatedData(array $data): Response
    {
        $this->em->getAuthTokenRepo()->createQueryBuilder('token')
            ->delete()
            ->where('token.source = :source')
            ->andWhere('token.sourceId IN (:source_ids)')
            ->setParameter('source_ids', $data['source_ids'])
            ->setParameter('source', $data['source'])
            ->getQuery()
            ->execute();

        return $this->json(['message' => "Users successfully revoked"]);
    }

    public function process(Request $request): Response
    {
        return new Response('ProcessValidatedData() is deprecated', 500);
    }
}
