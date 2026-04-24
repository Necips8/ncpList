<?php

namespace App\Controller;

use App\Entity\ShoppingList;
use App\Entity\ListItem;
use App\Repository\ShoppingListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/lists')]
class ListController extends AbstractController
{
    #[Route('', name: 'api_lists_get_all', methods: ['GET'])]
    public function getLists(ShoppingListRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $user = $this->getUser();
        $lists = $repository->findBy(['owner' => $user]);

        $data = $serializer->normalize($lists, null, ['groups' => 'list:read']);
        return new JsonResponse($data);
    }

    #[Route('', name: 'api_lists_create', methods: ['POST'])]
    public function createList(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();

        $list = new ShoppingList();
        $list->setName($data['name'] ?? 'Neue Liste');
        $list->setDescription($data['description'] ?? null);
        $list->setOwner($user);

        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $itemData) {
                $item = new ListItem();
                $item->setName($itemData['name'] ?? 'Item');
                $item->setAmount($itemData['amount'] ?? 1);
                $list->addItem($item);
                $entityManager->persist($item);
            }
        }

        $entityManager->persist($list);
        $entityManager->flush();

        $normalized = $serializer->normalize($list, null, ['groups' => 'list:read']);
        return new JsonResponse($normalized, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_lists_delete', methods: ['DELETE'])]
    public function deleteList(string $id, ShoppingListRepository $repository, EntityManagerInterface $entityManager): JsonResponse
    {
        $list = $repository->find($id);

        if (!$list) {
            return new JsonResponse(['error' => 'List not found'], Response::HTTP_NOT_FOUND);
        }

        $this->denyAccessUnlessGranted('LIST_DELETE', $list);

        $entityManager->remove($list);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
