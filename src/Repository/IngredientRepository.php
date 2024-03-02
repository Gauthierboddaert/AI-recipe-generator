<?php

namespace App\Repository;

use App\Entity\Ingredient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ingredient>
 *
 * @method Ingredient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ingredient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ingredient[]    findAll()
 * @method Ingredient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IngredientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ingredient::class);
    }

    public function findIngredientLikeName(string $name): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.name LIKE :name')
            ->setParameter('name', $name . '%')
            ->getQuery()
            ->getResult();
    }

    public function findSomesRandomIngredients(int $limit): array
    {
        return $this->createQueryBuilder('i')
            ->setMaxResults($limit)
            ->orderBy('RAND()')
            ->getQuery()
            ->getResult();
    }
}
