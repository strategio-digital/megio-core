<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.dev, jz@strategio.dev)
 */
declare(strict_types=1);

namespace Megio\Http\Request\Auth;

use Megio\Database\CrudHelper\CrudHelper;
use Megio\Database\EntityManager;
use Nette\Schema\Expect;
use Megio\Database\Interface\IAuthenticable;
use Megio\Http\Request\Request;
use Symfony\Component\HttpFoundation\Response;

class RevokeTokenRequest extends Request
{
    public function __construct(private readonly EntityManager $em, private readonly CrudHelper $crudHelper)
    {
    }
    
    public function schema(): array
    {
        $all = $this->crudHelper->getAllEntities();
        $filtered = array_filter($all, fn($item) => is_subclass_of($item['value'], IAuthenticable::class));
        $tables = array_map(fn($class) => $class['table'], $filtered);
        
        return [
            'source_ids' => Expect::arrayOf('string')->required(),
            'source' => Expect::anyOf(...$tables),
        ];
    }
    
    public function process(array $data): Response
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
}