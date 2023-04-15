<?php

namespace App\Repository;

use App\Entity\FavouriteTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FavouriteTable>
 *
 * @method FavouriteTable|null find($id, $lockMode = null, $lockVersion = null)
 * @method FavouriteTable|null findOneBy(array $criteria, array $orderBy = null)
 * @method FavouriteTable[]    findAll()
 * @method FavouriteTable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavouriteTableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavouriteTable::class);
    }

    public function add(FavouriteTable $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FavouriteTable $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
