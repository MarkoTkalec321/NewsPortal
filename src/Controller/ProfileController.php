<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\SavedArticlesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    private $categoryRepository;
    private $articleRepository;
    private $savedArticlesRepository;
    private $userRepository;

    public function __construct(CategoryRepository $categoryRepository,
                                ArticleRepository $articleRepository,
                                SavedArticlesRepository $savedArticlesRepository,
                                UserRepository $userRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->articleRepository = $articleRepository;
        $this->savedArticlesRepository = $savedArticlesRepository;
        $this->userRepository = $userRepository;
    }
    #[Route('/profile/{id}', name: 'app_profile')]
    public function profile(int $id): Response
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        $savedArticles = $this->savedArticlesRepository->findByUser($user);
        $categories = $this->categoryRepository->findAll();

        return $this->render('profile/profile.html.twig', [
            'user' => $user,
            'categories' => $categories,
            'savedArticles' => $savedArticles,
        ]);
    }

    #[Route('/profile/{userId}/undo-save/{articleId}', name: 'app_profile_undo_save', methods: ['POST'])]
    public function undoSaveArticle(
        int $userId,
        int $articleId,
        EntityManagerInterface $em,
        Request $request
    ): Response {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        if ($user !== $this->getUser()) {
            throw $this->createAccessDeniedException('You can only manage your own profile.');
        }

        $article = $this->articleRepository->find($articleId);

        if (!$article) {
            throw $this->createNotFoundException('Article not found.');
        }

        $savedArticle = $this->savedArticlesRepository->findOneBy(['user' => $user, 'article' => $article]);

        if ($savedArticle) {
            $token = $request->request->get('_token');
            if (!$this->isCsrfTokenValid('undo_save' . $articleId, $token)) {
                throw $this->createAccessDeniedException('Invalid CSRF token.');
            }

            $article->removeSavedArticle($savedArticle);
            $em->remove($savedArticle);
            $em->flush();
        }

        return $this->redirectToRoute('app_profile', ['id' => $userId]);
    }

}
