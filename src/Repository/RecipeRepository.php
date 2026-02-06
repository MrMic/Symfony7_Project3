<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    // ── INFO: CONSTRUCTOR ───────────────────────────────────────────────
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Recipe::class);
    }

    // ______________________________________________________________________
    /**
     * Paginate the ist of recipes
     *
     * @param Request $request the input request with query parameters
     * @return SlidingPagination
     */
    public function paginateRecipes(int $page): SlidingPagination
    {
        return $this->paginator->paginate(
            $this->createQueryBuilder('r')
                ->leftJoin('r.category', 'c')
                ->addSelect('r', 'c'),
            $page,
            20,
            [
                'distinct' => false,
                'sortFieldWhitelist' => ['r.id', 'r.title'],
            ]
        );

        /* return new Paginator(
            $this->createQueryBuilder('r')
                ->setFirstResult(($page - 1) * $limit)
                ->setMaxResults($limit)
                ->getQuery()
                ->setHint(Paginator::HINT_ENABLE_DISTINCT, false),
            false
        ); */
    }

    // ______________________________________________________________________
    /**
     * Genère la somme des durées de toutes les recettes
     * @return int
     */
    public function findTotalDuration(): int
    {
        return  $this->createQueryBuilder('r')
            ->select('SUM(r.duration) as total_duration')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // ______________________________________________________________________
    /**
     * Return recipes lower than duration
     *
     * @param int $duration
     * @return Recipe[]
     */
    public function findWithDurationLowerThan(int $duration): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.duration <= :duration')
            ->orderBy('r.duration', 'ASC')
            ->setMaxResults(10)
            ->setParameter('duration', $duration)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Recipe[] Returns an array of Recipe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Recipe
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
