<?php

namespace App\Controller\Api;

use App\Entity\Memory;
use App\Entity\Picture;
use App\Repository\MemoryRepository;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PictureController extends AbstractController
{
    /**
     * Display all pictures
     * @param PictureRepository $ppictureRepository
     * @return Response
     */
    #[Route('/api/pictures', methods: ['GET'])]
    public function index(PictureRepository $pictureRepository)
    {
        $pictures = $pictureRepository->findAll();

        return $this->json($pictures, 200, [], ['groups' => ['get_picture', 'get_memory_id']]);
    }

    /**
     * Display a single picture by its id
     * @param Picture $picture
     * @return Response
     */
    #[Route('/api/picture/{id<\d+>}', methods: ['GET'])]
    public function read(Picture $picture = null )
    {
        if (!$picture) {
            return $this->json(
                "Erreur : Photo inexistante", 404
            );
        }

        return $this->json($picture, 200, [], ['groups' => ['get_picture', 'get_memory_id']]
    );
    }

    /**
     * Create a new picture
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    #[Route('/api/create/picture', methods: ['POST'])]
    public function create(SerializerInterface $serializer, EntityManagerInterface $entityManager, Request $request)
    {
        $picture = $serializer->deserialize($request->getContent(), Picture::class, 'json');

        $entityManager->persist($picture);
        $entityManager->flush();

        return $this->json($picture, 201, []);
    }

    /**
     * Update a picture by its id
     * @param Picture $picture
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/api/update/picture/{id<\d+>}', methods: ['PUT'])]
    public function updatePicture(Picture $picture, MemoryRepository $memoryRepository, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
    
        if (!$picture) {
            return $this->json("Erreur : La photo n'existe pas", 404);
        }
        $jsonData = $request->getContent();
        $data = $serializer->decode($jsonData, 'json');
    
        $memoryId = $data['memory']['id'];
        $memory = $memoryRepository->find($memoryId);
        if (!$memory) {
            return $this->json("Erreur : Le souvenir associé n'existe pas", 404);
        }
        $picture->setMemory($memory);
        $picture->setPicture($data['picture']);

    $entityManager->flush();
    return $this->json(['message' => 'Les photos ont été mises à jour'], Response::HTTP_OK);
}


    /**
     * Delete a picture by its id
     */
    #[Route('/api/delete/picture/{id<\d+>}', methods: ['DELETE'])]
    public function delete(Picture $picture, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($picture);
        $entityManager->flush();

        return new Response('Photo supprimée', 200);
    }
}