<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
* @extends ServiceEntityRepository<Comment>
*
* @method Comment|null find($id, $lockMode = null, $lockVersion = null)
* @method Comment|null findOneBy(array $criteria, array $orderBy = null)
* @method Comment[]    findAll()
* @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
*/
class CommentRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Comment::class);
  }

  public function save(Comment $entity, bool $flush = false): void
  {
    $this->getEntityManager()->persist($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }

  public function remove(Comment $entity, bool $flush = false): void
  {
    $this->getEntityManager()->remove($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }



  public function findCommentsByFigurePaginated(int $figureId, int $page = 1, int $limit = 10): array
  {
    $limit = abs($limit);
    $qb = $this->createQueryBuilder('c');
    $qb->select('c')
    ->addSelect('u.pseudo, c.content, c.created_at')
    ->leftJoin('c.user', 'u')
    ->leftJoin('c.figure', 'f')
    ->where('f.id = :figure_id')
    ->setParameter('figure_id', $figureId)
    ->orderBy('c.created_at', 'DESC')
    ->setMaxResults($limit)
    ->setFirstResult(($page * $limit) - $limit);
    $paginator = new Paginator($qb);
    $data = $paginator->getQuery()->getResult();

    //On calcule le nombre de pages
    $pages = ceil($paginator->count() / $limit);

    $result['data'] = $data;
    $result['pages'] = $pages;
    $result['page'] = $page;
    $result['limit'] = $limit;

    return $result;

  }
}
