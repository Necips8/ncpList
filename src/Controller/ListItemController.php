<?php

namespace App\Controller;

use App\Entity\ListItem;
use App\Entity\ShoppingList;
use App\Repository\ListItemRepository;
use App\Repository\ShoppingListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/lists')]
class ListItemController extends AbstractController
{
    #[Route('/{id}/items', name: 'api_items_get_all', methods: ['GET'])]
    public function getItems(string $id, ShoppingListRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $list = $repository->find($id);

        if (!$list) {
            return new JsonResponse(['error' => 'List not found'], Response::HTTP_NOT_FOUND);
        }

        if ($list->getOwner() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $data = $serializer->normalize($list->getItems(), null, ['groups' => 'item:read']);
        return new JsonResponse($data);
    }

    #[Route('/{id}/items', name: 'api_items_add', methods: ['POST'])]
    public function addItem(string $id, Request $request, ShoppingListRepository $repository, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $list = $repository->find($id);

        if (!$list) {
            return new JsonResponse(['error' => 'List not found'], Response::HTTP_NOT_FOUND);
        }

        if ($list->getOwner() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);
        $item = new ListItem();
        $item->setName($data['name'] ?? 'Item');
        $item->setAmount($data['amount'] ?? 1);
        $item->setDescription($data['description'] ?? null);
        
        $list->addItem($item);
        $entityManager->persist($item);
        $entityManager->flush();

        $normalized = $serializer->normalize($list, null, ['groups' => 'list:read']);
        return new JsonResponse($normalized, Response::HTTP_CREATED);
    }

    #[Route('/items/{itemId}', name: 'api_items_get_one', methods: ['GET'])]
    public function getItem(string $itemId, ListItemRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $item = $repository->find($itemId);

        if (!$item) {
            return new JsonResponse(['error' => 'Item not found'], Response::HTTP_NOT_FOUND);
        }

        if ($item->getList()->getOwner() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $data = $serializer->normalize($item, null, ['groups' => 'item:read']);
        return new JsonResponse($data);
    }

    #[Route('/items/{itemId}', name: 'api_items_update', methods: ['PUT'])]
    public function updateItem(string $itemId, Request $request, ListItemRepository $repository, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $item = $repository->find($itemId);

        if (!$item) {
            return new JsonResponse(['error' => 'Item not found'], Response::HTTP_NOT_FOUND);
        }

        if ($item->getList()->getOwner() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['name'])) $item->setName($data['name']);
        if (isset($data['amount'])) $item->setAmount($data['amount']);
        if (isset($data['description'])) $item->setDescription($data['description']);
        if (isset($data['state'])) $item->setState($data['state']);

        $entityManager->flush();

        $normalized = $serializer->normalize($item, null, ['groups' => 'item:read']);
        return new JsonResponse($normalized);
    }

    #[Route('/items/{itemId}', name: 'api_items_delete', methods: ['DELETE'])]
    public function deleteItem(string $itemId, ListItemRepository $repository, EntityManagerInterface $entityManager): JsonResponse
    {
        $item = $repository->find($itemId);

        if (!$item) {
            return new JsonResponse(['error' => 'Item not found'], Response::HTTP_NOT_FOUND);
        }

        if ($item->getList()->getOwner() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $entityManager->remove($item);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
