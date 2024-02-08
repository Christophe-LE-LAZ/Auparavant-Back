<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\Place;
use App\Entity\Memory;
use App\Entity\Location;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlaceAndMemoryController extends AbstractController
{
    #[Route('/api/create/place-and-memory', methods: ['POST'])]
    public function create(Place $place, Location $location, SerializerInterface $serializer, EntityManagerInterface $entityManager, Request $request)
    {
        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true);

        $place = $serializer->deserialize(json_encode($data['place']), Place::class, 'json');

        $memory = $serializer->deserialize(json_encode($data['memory']), Memory::class, 'json');

        $location = $serializer->deserialize(json_encode($data['location']), Location::class, 'json');

        $user = $serializer->deserialize(json_encode($data['User']), User::class, 'json');

        if($place){
            $place->setLocation($location);
        }

        $memory->setUser($user);
        $memory->setLocation($location);

    }
}
