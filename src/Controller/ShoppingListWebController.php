<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ShoppingListWebController extends AbstractController
{
    public function __construct(
        private AuthorizationCheckerInterface $authChecker,
    ) {}

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        if (!$this->authChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('shopping_list/index.html.twig');
    }

    #[Route('/lists/{id}', name: 'app_list_detail')]
    public function detail(string $id): Response
    {
        if (!$this->authChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('shopping_list/detail.html.twig', [
            'listId' => $id,
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        // Falls bereits eingeloggt, zur Startseite weiterleiten
        if ($this->authChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('shopping_list/login.html.twig');
    }

    #[Route('/login_check', name: 'app_login_check')]
    public function loginCheck(): Response
    {
        // Diese Route wird von Symfony Security automatisch behandelt
        // Sie sollte nie direkt aufgerufen werden
        throw new \RuntimeException('You must not call this route directly');
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Diese Route wird von Symfony Security automatisch behandelt
        throw new \RuntimeException('You must not call this route directly');
    }
}
