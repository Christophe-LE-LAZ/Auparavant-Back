<?php

namespace App\Controller\Api;

use DateTime;
use App\Entity\Place;
use App\Entity\Memory;
use DateTimeImmutable;
use App\Entity\Picture;
use App\Entity\Location;
use App\Repository\UserRepository;
use App\Repository\PlaceRepository;
use App\Repository\MemoryRepository;
use App\Repository\PictureRepository;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * This controller groups together all the methods that manage memories.
 * One method displays all memories.
 * One displays only one.
 * Two methods create a memory:
 * -> One creates a memory from an existing locality and creates the name and type of the place if the existing ones are not suitable for this memory.
 * -> Another creates a memory and a new locality as well as the name and type of the corresponding place.
 * One updates a memory with its id by adding, modifying or deleting additional photos.
 * One last deletes a memory by its id and the data assigned to it.
 */
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

        return $this->json($memories, 200, [], ['groups' => ['get_memory', 'get_location', 'get_place', 'get_user']]);
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
                "Erreur : Souvenir inexistant", 404
            );
        }

        return $this->json($memory, 200, [], ['groups' => ['get_memory', 'get_location', 'get_place', 'get_user']]
    );
    }

    /**
     * Create a new memory
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    #[Route('/api/secure/create/memory', methods: ['POST'])]
    public function create(SerializerInterface $serializer, EntityManagerInterface $entityManager, Request $request)
    {
        $memory = $serializer->deserialize($request->getContent(), Memory::class, 'json');

        $entityManager->persist($memory);
        $entityManager->flush();

        return $this->json($memory, 201, []);
    }

   
    /**
     * First method for creating a memory
     * Create a new memory as well as the name and type of place from a location selected on the map
     * ! Or
     * Create a new memory by selecting the name and type of a pre-existing place from a location selected on the map.
     * 
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param LocationRepository $locationRepository
     * @param UserRepository $userRepository
     * @param PlaceRepository $placeRepository
     * @return Response
     * 
     */
    #[Route('/api/secure/create/memory-and-place', methods: ['POST'])]
    public function createMemoryAndPlace(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, LocationRepository $locationRepository, PlaceRepository $placeRepository)
    {
        $jsonContent = $request->getContent();
        // $jsonContent = {"user":{"id":1},"location":{"id":1},"place":{"create_new_place":true,"name":"l'elysée","type":"batiment"},"memory":{"title":"l'elysée en 1990","content":"que de souvenirs avec ce lieu","picture_date":"1990-02-08T14:00:00Z","main_picture":"URL","additional_pictures":["URL_image_1","URL_image_2"]}}
   
        $jsonContent = trim($jsonContent);
        $data = json_decode($jsonContent, true);
     
        $user = $userRepository->find($data['user']['id']);
        $location = $locationRepository->find($data['location']['id']);
        
        $placeData = $data['place'];
        if ($placeData['create_new_place'] == true) {
        $newPlace = (new Place())
            ->setName($placeData['name'])
            ->setType($placeData['type'])
            ->setLocation($location);
        $entityManager->persist($newPlace);
        $entityManager->flush();
        $place = $placeRepository->find($newPlace); 
        }
        else {
            $place = $placeRepository->find($data['place']['id']);
        }
    
        $memoryData = $data['memory'];
        // dd($memory);
        $newMemory = (new Memory())
            ->setTitle($memoryData['title'])
            ->setContent($memoryData['content'])
            ->setPictureDate(new DateTime($memoryData['picture_date']))
            ->setMainPicture($memoryData['main_picture'])
            ->setUser($user)
            ->setLocation($location)
            ->setPlace($place);

        $entityManager->persist($newMemory);
                   

        // additional image management //
         if (isset($memoryData['additional_pictures']) && is_array($memoryData['additional_pictures'])) {
             foreach ($memoryData['additional_pictures'] as $additionalPictureUrl) {
                 $additionalPicture = (new Picture())
                    ->setPicture($additionalPictureUrl)
                    ->setMemory($newMemory);
                 $entityManager->persist($additionalPicture);
             }
        }
               $entityManager->flush();
        return $this->json(['message' => 'Souvenir créé'], Response::HTTP_CREATED);
    }

    /**
     * Second method for creating a memory
     * TODO: Create a new memory including name, type and location
     * 
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @return Response
     * 
     */
    #[Route('/api/secure/create/memory-and-location-and-place', methods: ['POST'])]
    public function createMemoryAndLocationAndPlace(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $jsonContent = $request->getContent();
        // $jsonContent = {"user":{"id":1},"location":{"area": "xxx", "department": "xxx", "district": "xxx", "street": "xxx rue xxx", "city": "xxx", "zipcode": "00000", "latitude" : "00.000", "longitude": "0.0000"},"place":{"name":"l'elysée","type":"batiment"},"memory":{"title":"l'elysée en 1990","content":"que de souvenirs avec ce lieu","picture_date":"1990-02-08T14:00:00Z","main_picture":"URL","additional_pictures":["URL_image_1","URL_image_2"]}}

        $jsonContent = trim($jsonContent);
        $data = json_decode($jsonContent, true);
        
        $user = $userRepository->find($data['user']['id']);
        $locationData = $data['location'];
        $newLocation = (new Location ())
            ->setArea($locationData['area'])
            ->setDepartment($locationData['department'])
            ->setDistrict($locationData['district'])
            ->setStreet($locationData['street'])
            ->setCity($locationData['city'])
            ->setZipcode($locationData['zipcode'])
            ->setLatitude($locationData['latitude'])
            ->setLongitude($locationData['longitude']);
        $entityManager->persist($newLocation);

        $placeData = $data['place'];
        $newPlace = (new Place())
            ->setName($placeData['name'])
            ->setType($placeData['type'])
            ->setLocation($newLocation);
        $entityManager->persist($newPlace);

        $memoryData = $data['memory'];
        // dd($memory);
        $newMemory = (new Memory())
            ->setTitle($memoryData['title'])
            ->setContent($memoryData['content'])
            ->setPictureDate(new DateTime($memoryData['picture_date']))
            ->setMainPicture($memoryData['main_picture'])
            ->setUser($user)
            ->setPlace($newPlace)
            ->setLocation($newLocation);


        $entityManager->persist($newMemory);
                   


        // additional picture management //
         if (isset($memoryData['additional_pictures']) && is_array($memoryData['additional_pictures'])) {
            foreach ($memoryData['additional_pictures'] as $additionalPictureUrl) {
                $additionalPicture = (new Picture())
                    ->setPicture($additionalPictureUrl)
                    ->setMemory($newMemory);
                $entityManager->persist($additionalPicture);
            }
        }
         $entityManager->flush();
        return $this->json(['message' => 'Souvenir créé'], Response::HTTP_CREATED);
    }

    /**
     * Update a memory by its id
     * @param Memory $memory
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/api/secure/update/memory/{id<\d+>}', methods: ['PUT'])]
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

        return $this->json($memory, 200, [], ['groups' => ['get_memory']]);
    }

    /**
     * TODO : Update a memory by its id
     * 
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param PlaceRepository $placeRepository
     * @param MemoryRepository $memoryRepository
     * @param PictureRepository $pictureRepository
     * @return Response
     * 
     */
    #[Route('/api/update/memory-and-place/{id<\d+>}', methods: ['PUT'])]
    public function updateMemoryAndPlace(Request $request, EntityManagerInterface $entityManager, PlaceRepository $placeRepository, MemoryRepository $memoryRepository, PictureRepository $pictureRepository)
    {
        $jsonContent = $request->getContent();
        // $jsonContent ={"place":{"update_place":true,"id":1,"name":"nouveau_nom","type":"nouveau_type"},"memory":{"id":1,"title":"nouveau_titre","content":"nouveau_contenu","picture_date":"1890-02-08T14:00:00Z","main_picture":"nouvelle_URL","additional_pictures":[{"id":1,"URL_image":"nouvelle_URL_image_12"},{"id":2,"URL_image":"nouvelle_URL_image_24"},{"URL_image":"nouvelle_URL_image_38"}]}}

   
        $jsonContent = trim($jsonContent);
        $data = json_decode($jsonContent, true);
           
        $placeData = $data['place'];
        if ($placeData['update_place'] == true) {
        $currentPlace = $placeRepository->find($data['place']['id'])
            ->setName($placeData['name'])
            ->setType($placeData['type'])
            ->setUpdatedAt(new DateTimeImmutable());
        $entityManager->persist($currentPlace);
        $entityManager->flush();
        }
        
    
        $memoryData = $data['memory'];
    
        $currentMemory = $memoryRepository->find($data['memory']['id'])
            ->setTitle($memoryData['title'])
            ->setContent($memoryData['content'])
            ->setPictureDate(new DateTime($memoryData['picture_date']))
            ->setMainPicture($memoryData['main_picture'])
            ->setUpdatedAt(new DateTimeImmutable());
            // ->setPlace($place);

        $entityManager->persist($currentMemory);
                   

        // additional picture management //
        foreach ($memoryData['additional_pictures'] as $additionalPictureData) {
            $additionalPictureId = isset($additionalPictureData['id']) ? $additionalPictureData['id'] : null;
        
            if ($additionalPictureId) {
                // Update an existing picture
                $additionalPicture = $pictureRepository->find($additionalPictureId);
                if ($additionalPicture) {
                    $additionalPicture
                        ->setPicture($additionalPictureData['URL_image'])
                        ->setMemory($currentMemory)
                        ->setUpdatedAt(new DateTimeImmutable());
        
                    $entityManager->persist($additionalPicture);
                }
            } else {
                // Create a new picture
                $newAdditionalPicture = new Picture();
                $newAdditionalPicture
                    ->setPicture($additionalPictureData['URL_image'])
                    ->setMemory($currentMemory);
        
                $entityManager->persist($newAdditionalPicture);
            }
        }
               $entityManager->flush();
        return $this->json(['message' => 'Souvenir mis à jour'], Response::HTTP_OK);
    }

    /**
     * Delete a memory by its id
     */
    #[Route('/api/secure/delete/memory/{id<\d+>}', methods: ['DELETE'])]
    public function delete(Memory $memory, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($memory);
        $entityManager->flush();

        return new Response('Souvenir supprimé', 200);
    }
}