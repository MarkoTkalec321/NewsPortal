<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\SavedArticlesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    private $categoryRepository;
    private $articleRepository;
    private $savedArticlesRepository;
    private $userRepository;
    private $commentRepository;

    public function __construct(CategoryRepository $categoryRepository,
                                ArticleRepository $articleRepository,
                                SavedArticlesRepository $savedArticlesRepository,
                                UserRepository $userRepository,
                                CommentRepository $commentRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->articleRepository = $articleRepository;
        $this->savedArticlesRepository = $savedArticlesRepository;
        $this->userRepository = $userRepository;
        $this->commentRepository = $commentRepository;
    }
    #[Route('/admin/create-article', name: 'app_admin')]
    public function admin(Request $request): Response
    {
        $articles = $this->articleRepository->findAll();
        $categories = $this->categoryRepository->findAll();

        $article = new Article();
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move($this->getParameter('upload_directory'), $newFilename);
                $article->setImage('uploads/images/' . $newFilename);
            }

            $article->setCreatedAt(new \DateTimeImmutable());

            $this->articleRepository->save($article);

            $this->addFlash('success', 'Article created successfully!');
            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/admin.html.twig', [
            'articles' => $articles,
            'categories' => $categories,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/update-delete-article', name: 'app_admin_update_delete_article')]
    public function manageArticles(ArticleRepository $articleRepository): Response
    {
        $categories = $this->categoryRepository->findAll();
        $articles = $articleRepository->findAll();

        return $this->render('admin/update_delete_article.html.twig', [
            'categories' => $categories,
            'articles' => $articles,
        ]);
    }

    #[Route('/admin/update-article/{id}', name: 'app_admin_update_article')]
    public function updateArticle(Request $request, ArticleRepository $articleRepository, int $id) : Response
    {
        $categories = $this->categoryRepository->findAll();
        $article = $articleRepository->find($id);

        if (!$article) {
            throw $this->createNotFoundException('Article not found.');
        }

        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move($this->getParameter('upload_directory'), $newFilename);
                $article->setImage('uploads/images/' . $newFilename);
            }

            $article->setUpdatedAt(new \DateTimeImmutable());
            $articleRepository->save($article);

            $this->addFlash('success', 'Article updated successfully!');
            return $this->redirectToRoute('app_admin_update_delete_article');
        }
        return $this->render('admin/edit_article.html.twig', [
            'categories' => $categories,
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/delete-article/{id}', name: 'app_admin_delete_article')]
    public function deleteArticle(ArticleRepository $articleRepository, int $id): Response
    {
        $article = $articleRepository->find($id);
        if (!$article) {
            throw $this->createNotFoundException('Article not found.');
        }

        $savedArticles = $this->savedArticlesRepository->findBy(['article' => $article]);

        foreach ($savedArticles as $savedArticle) {
            $this->savedArticlesRepository->remove($savedArticle);
        }
        $articleRepository->remove($article);
        $this->addFlash('success', 'Article deleted successfully!');

        return $this->redirectToRoute('app_admin_update_delete_article');
    }

    #[Route('/admin/comment-delete/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function deleteComment(int $id, EntityManagerInterface $em, Request $request): Response
    {
        $comment = $this->commentRepository->find($id);

        if (!$comment) {
            throw $this->createNotFoundException('Comment not found.');
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Only admins can delete comments.');
        }

        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete_comment' . $id, $token)) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $em->remove($comment);
        $em->flush();

        return $this->redirectToRoute('app_article_show', ['id' => $comment->getArticle()->getId()]);
    }
}

