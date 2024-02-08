<?php

namespace App\Controller\Api;

use App\Entity\Memory;
use Doctrine\ORM\EntityManager;
use App\Repository\MemoryRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MemoryController extends AbstractController
{
    /**
     * Display all memories
     * @param MemoryRepository $memoryRepository
     * @return Response
     */
    #[Route('/api/memories', methods: ['GET'])]
    public function index(MemoryRepository $memoryRepository)
    {
        $memories = $memoryRepository->findAll();

        return $this->json($memories, 200, [], ['groups' => ['get_memory']]);
    }

    /**
     * Display a single memory by its id
     * @param Memory $memory
     * @return Response
     */
    #[Route('/api/memory/{id<\d+>}', methods: ['GET'])]
    public function read(Memory $memory = null )
    {
        if (!$memory) {
            return $this->json(
                "Error : Souvenir inexistant", 404
            );
        }

        return $this->json($memory, 200, [], ['groups' => ['get_memory']]
    );
    }

    /**
     * Create a new memory
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    #[Route('/api/create/memory', methods: ['POST'])]
    public function create(SerializerInterface $serializer, EntityManagerInterface $entityManager, Request $request)
    {
        $memory = $serializer->deserialize($request->getContent(), Memory::class, 'json');

        $entityManager->persist($memory);
        $entityManager->flush();

        return $this->json($memory, 201, []);
    }

    /**
     * Update a memory by its id
     * @param Memory $memory
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return Response
     */
    #[Route('/api/update/memory/{id<\d+}', methods: ['PUT'])]
    public function update(Memory $memory = null, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        if(!$memory) {
            return $this->json(
                "Erreur : Le souvenir n'existe pas", 404
            );
        }
        $serializer->deserialize($request->getContent(), Memory::class, 'json', ['object_to_populate'=>$memory]);
        $memory->setUpdatedAt(new DateTimeImmutable());

        $entityManager->flush();

        return $this->json($memory, 200, []);
    }

    /**
     * Delete a memory by its id
     */
    #[Route('/api/delete/memory/{id<\d+>}', methods: ['DELETE'])]
    public function delete(Memory $memory, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($memory);
        $entityManager->flush();

        return new Response('Souvenir supprime', 200);
    }
}