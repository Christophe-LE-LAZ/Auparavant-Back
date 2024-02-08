<?php

namespace App\Controller\Api;

use App\Entity\Place;
use DateTimeImmutable;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlaceController extends AbstractController
{
    #[Route('/api/places', methods: ['GET'])]
    public function index(PlaceRepository $placeRepository)
    {
        $places = $placeRepository->findAll();

        return $this->json($places, 200, [], ['groups' => ['get_place']]);
    }

    #[Route('/api/place/{id<\d+>}', methods: ['GET'])]
    public function read(Place $place = null )
    {
        if (!$place) {
            return $this->json(
                "Error : Endroit inexistant", 404
            );
        }

        return $this->json($place, 200, [], ['groups' => ['get_place']]
    );
    }

    #[Route('/api/create/place', methods: ['POST'])]
    public function create(SerializerInterface $serializer, EntityManagerInterface $entityManager, Request $request)
    {
        $place = $serializer->deserialize($request->getContent(), Place::class, 'json');

        $entityManager->persist($place);
        $entityManager->flush();

        return $this->json($place, 201, []);
    }

    #[Route('/api/update/place/{id<\d+>}', methods: ['PUT'])]
    public function update(Place $place = null,  Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        if(!$place) {
            return $this->json(
                "Erreur : L'endroit n'existe pas", 404
            );
        }
        $serializer->deserialize($request->getContent(), Place::class, 'json', ['object_to_populate'=>$place]);
        $place->setUpdatedAt(new DateTimeImmutable());

        $entityManager->flush();

        return $this->json($place, 200, [], ['groups' => ['get_place']]);
    }

    #[Route('/api/delete/place/{id<\d+>}', methods: ['DELETE'])]
    public function delete(Place $place, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($place);
        $entityManager->flush();

        return new Response('Endroit supprime', 200);
    }
}