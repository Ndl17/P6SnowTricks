<?php

namespace App\Repository;

use App\Entity\Videos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
* @extends ServiceEntityRepository<Videos>
*
* @method Videos|null find($id, $lockMode = null, $lockVersion = null)
* @method Videos|null findOneBy(array $criteria, array $orderBy = null)
* @method Videos[]    findAll()
* @method Videos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
*/
class VideosRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Videos::class);
  }

  public function save(Videos $entity, bool $flush = false): void
  {
    $this->getEntityManager()->persist($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }

  public function remove(Videos $entity, bool $flush = false): void
  {
    $this->getEntityManager()->remove($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }

}
