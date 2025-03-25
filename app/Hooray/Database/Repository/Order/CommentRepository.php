<?php
declare(strict_types=1);

namespace App\Hooray\Database\Repository\Order;

use App\Hooray\Database\Entity\Order\Comment;
use App\Hooray\Database\Entity\Order\Order;
use Doctrine\ORM\EntityRepository;

/**
 * @method Comment|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method Comment|NULL findOneBy(array $criteria, array $orderBy = NULL)
 * @method Comment[] findAll()
 * @method Comment[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
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