<?php

namespace App\Controller\Api;

use App\Entity\Picture;
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

        return $this->json($pictures, 200, [], ['groups' => ['get_picture']]);
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
                "Error : Photo inexistante", 404
            );
        }

        return $this->json($picture, 200, [], ['groups' => ['get_picture']]
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
    public function update(Picture $picture = null, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        if(!$picture) {
            return $this->json(
                "Erreur : La photo n'existe pas", 404
            );
        }
        $serializer->deserialize($request->getContent(), Picture::class, 'json', ['object_to_populate'=>$picture]);

        $entityManager->flush();

        return $this->json($picture, 200, []);
    }

    /**
     * Delete a picture by its id
     */
    #[Route('/api/delete/picture/{id<\d+>}', methods: ['DELETE'])]
    public function delete(Picture $picture, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($picture);
        $entityManager->flush();

        return new Response('Photo supprime', 200);
    }
}