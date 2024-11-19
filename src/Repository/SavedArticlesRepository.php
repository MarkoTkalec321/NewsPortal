<?php

namespace App\Repository;

use App\Entity\SavedArticles;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SavedArticles>
 */
class SavedArticlesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SavedArticles::class);
    }

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function remove(SavedArticles $savedArticle, bool $flush = true): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($savedArticle);
        if ($flush) {
            $entityManager->flush();
        }
    }

}
