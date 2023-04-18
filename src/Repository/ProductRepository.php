<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{


    /**
     * ProductRepository constructor.
     *
     * @param ManagerRegistry $registry The registry.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    // End of ProductRepository::__construct


    /**
     * Retrieves all products with pagination.
     *
     * @param int $offset The offset of the first result.
     * @param int $limit The maximum number of results to retrieve.
     *
     * @return array The products.
     */
    public function findAllWithPagination(int $offset, int $limit): array
    {
        $qb = $this->createQueryBuilder('product')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit);
        return $qb->getQuery()->getResult();
    }


    /**
     * Saves a product.
     *
     * @param Product $entity
     * @param bool $flush
     * @return void
     */
    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * Removes a product.
     *
     * @param Product $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
