<?php
declare(strict_types=1);

namespace App\Database\Repository\Order;

use App\Database\Entity\Order\Comment;
use App\Database\Entity\Order\Order;
use Doctrine\ORM\EntityRepository;

/**
 * @method \App\Database\Entity\Order\Comment|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method \App\Database\Entity\Order\Comment|NULL findOneBy(array $criteria, array $orderBy = NULL)
 * @method \App\Database\Entity\Order\Comment[] findAll()
 * @method \App\Database\Entity\Order\Comment[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends EntityRepository<CommentRepository>
 */
class CommentRepository extends EntityRepository
{
    public function create(Order $order, string $source, string $message): Comment
    {
        $comment = new Comment();
        $comment->setOrder($order);
        $comment->setSource($source);
        $comment->setMessage($message);
        
        $this->_em->persist($comment);
        
        return $comment;
    }
}