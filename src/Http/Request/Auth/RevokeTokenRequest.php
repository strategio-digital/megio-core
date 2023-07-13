<?php
/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
declare(strict_types=1);

namespace Saas\Http\Request\Auth;

use Saas\Database\CrudHelper\CrudHelper;
use Saas\Database\EntityManager;
use Nette\Schema\Expect;
use Saas\Database\Interface\IAuthenticable;
use Saas\Http\Request\Request;
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
        $this->em->getAuthTokenRepo()->createQueryBuilder('Token')
            ->delete()
            ->where('Token.source = :source')
            ->andWhere('Token.sourceId IN (:source_ids)')
            ->setParameter('source_ids', $data['source_ids'])
            ->setParameter('source', $data['source'])
            ->getQuery()
            ->execute();
        
        return $this->json(['message' => "Users successfully revoked"]);
    }
}