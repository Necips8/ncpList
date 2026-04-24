<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ShoppingListWebController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // Falls nicht eingeloggt, zum Login leiten (später)
        // Für den Prototyp zeigen wir die Hauptseite
        return $this->render('shopping_list/index.html.twig');
    }

    #[Route('/lists/{id}', name: 'app_list_detail')]
    public function detail(string $id): Response
    {
        return $this->render('shopping_list/detail.html.twig', [
            'listId' => $id,
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('shopping_list/login.html.twig');
    }
}
