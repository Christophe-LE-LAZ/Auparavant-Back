<?php

namespace App\Controller\Api;

use App\Entity\Location;
use App\Repository\LocationRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LocationController extends AbstractController
{
    #[Route('/api/locations', methods: ['GET'])]
    public function index(LocationRepository $locationRepository)
    {
        $locations = $locationRepository->findAll();

        return $this->json(
            $locations, 200, [], ['groups' => ['get_location']]);

    }

    #[Route('/api/location/{id<\d+>}', methods: ['GET'])]
    public function read(Location $location = null )
    {
        if (!$location) {
            return $this->json(
                "Error : Localite inexistante", 404
            );
        }

        return $this->json($location, 200, [], ['groups' => ['get_location']]
    );
    }

    #[Route('/api/create/location', methods: ['POST'])]
    public function create(SerializerInterface $serializer, EntityManagerInterface $entityManager, Request $request)
    {
        $location = $serializer->deserialize($request->getContent(), Location::class, 'json');

        $entityManager->persist($location);
        $entityManager->flush();

        return $this->json($location, 201, []);
    }

    #[Route('/api/update/location/{id<\d+>}', methods: ['PUT'])]
    public function update(Location $location = null, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        if(!$location) {
            return $this->json(
                "Erreur : La localite n'existe pas", 404
            );
        }
        $serializer->deserialize($request->getContent(), Location::class, 'json', ['object_to_populate'=>$location]);
        $location->setUpdatedAt(new DateTimeImmutable());

        $entityManager->flush();

        return $this->json($location, 200, []);
    }

    #[Route('/api/delete/location', methods: ['DELETE'])]
    public function delete()
    {
        
    }
}