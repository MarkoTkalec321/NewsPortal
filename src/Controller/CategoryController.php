<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    private $categoryRepository;
    private $articleRepository;

    public function __construct(CategoryRepository $categoryRepository, ArticleRepository $articleRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->articleRepository = $articleRepository;
    }

    #[Route('/category/{id}', name: 'app_category')]
    public function category(int $id): Response
    {
        $category = $this->categoryRepository->find($id);
        $articles = $this->articleRepository->findBy(['category' => $category]);
        $categories = $this->categoryRepository->findAll();

        return $this->render('category/category.html.twig', [
            'category' => $category,
            'articles' => $articles,
            'categories' => $categories,
        ]);
    }

}
