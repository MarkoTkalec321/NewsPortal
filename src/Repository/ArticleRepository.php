<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function save(Article $article, bool $flush = true): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($article);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(Article $article, bool $flush = true): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($article);

        if ($flush) {
            $entityManager->flush();
        }
    }
}
