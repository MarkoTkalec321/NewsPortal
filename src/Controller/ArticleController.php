<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Like;
use App\Entity\SavedArticles;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\LikeRepository;
use App\Repository\SavedArticlesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\CommentFormType;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends AbstractController
{
    private $em;
    private $articleRepository;
    private $categoryRepository;
    private $savedArticlesRepository;
    private $likeRepository;

    public function __construct(EntityManagerInterface $em,
                                ArticleRepository $articleRepository,
                                CategoryRepository $categoryRepository,
                                SavedArticlesRepository $savedArticlesRepository,
                                LikeRepository $likeRepository)
    {
        $this->em = $em;
        $this->articleRepository = $articleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->savedArticlesRepository = $savedArticlesRepository;
        $this->likeRepository = $likeRepository;
    }

    #[Route('/home', name: 'app_article')]
    public function index(): Response
    {
        $categories = $this->categoryRepository->findAll();

        return $this->render('article/index.html.twig', [
            'categories' => $categories,
            'articles' => $this->articleRepository->findAll(),
        ]);
    }

    #[Route('/article/{id}', name: 'app_article_show')]
    public function show(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $article = $this->articleRepository->find($id);
        $categories = $this->categoryRepository->findAll();
        $likes = $article->getLikes();

        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }

        $comment = new Comment();
        $comment->setArticle($article);

        if ($this->getUser()) {
            $comment->setAuthor($this->getUser());
        }

        $comment->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('app_article_show', ['id' => $id]);
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'categories' => $categories,
            'likes' => $likes,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/article/{id}/save', name: 'app_article_save')]
    public function saveArticle(int $id, EntityManagerInterface $em): Response
    {
        $article = $this->articleRepository->find($id);

        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }

        if ($this->getUser()) {
            $savedArticle = new SavedArticles();
            $savedArticle->setUser($this->getUser());
            $savedArticle->setArticle($article);

            $article->addSavedArticle($savedArticle);
            $em->persist($savedArticle);
            $em->flush();
        }

        return $this->redirectToRoute('app_article_show', ['id' => $id]);
    }

    #[Route('/article/{id}/undo-save', name: 'app_article_undo_save')]
    public function undoSaveArticle(int $id, EntityManagerInterface $em): Response
    {
        $article = $this->articleRepository->find($id);

        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }

        if ($this->getUser()) {
            $savedArticle = $this->savedArticlesRepository->findOneBy(['user' => $this->getUser(), 'article' => $article]);

            if ($savedArticle) {
                $article->removeSavedArticle($savedArticle);
                $em->remove($savedArticle);
                $em->flush();
            }
        }

        return $this->redirectToRoute('app_article_show', ['id' => $id]);
    }

    #[Route('/article/{id}/like', name: 'app_article_like')]
    public function likeArticle(int $id, EntityManagerInterface $em): Response
    {
        $article = $this->articleRepository->find($id);

        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }

        if ($this->getUser()) {

            $existingLike = $this->likeRepository->findOneBy(['user' => $this->getUser(), 'article' => $article]);

            if ($existingLike) {
                $em->remove($existingLike);
            } else {
                $like = new Like();
                $like->setUser($this->getUser());
                $like->setArticle($article);
                $like->setCreatedAt(new \DateTimeImmutable());

                $em->persist($like);
            }

            $em->flush();
        }

        return $this->redirectToRoute('app_article_show', ['id' => $id]);
    }

}
