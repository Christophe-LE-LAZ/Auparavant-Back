<?php

namespace App\Controller\Api;

use DateTime;
use App\Entity\User;
use App\Entity\Place;
use App\Entity\Memory;
use App\Entity\Location;
use App\Repository\UserRepository;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MemoryAndPlaceController extends AbstractController
{
    /**
     * Create a new memory as well as the name and type of place from a location selected on the map
     * 
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * 
     */
    #[Route('/api/create/memory-and-place', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, LocationRepository $locationRepository)
    {
        $jsonContent = $request->getContent();
        // $jsonContent = '{"user": {"id": 1},"location": {"id": 1},"place": {"name": "l'elysée","type": "batiment"},"memory":{"title": "l'elysée en 1990","content": "que de souvenirs avec ce lieu","picture_date": "1990-02-08T14:00:00Z","main_picture": "URL"}}'; 
        //{
        //   "user": {
        //     "id": 1
        //   },
        //   "location": {
        //     "id": 1
        //   },
        //   "place": {
        //     "name": "l'elysée",
        //     "type": "batiment"
        //   },
        //   "memory": {
        //     "title": "l'elysée en 1990",
        //     "content": "que de souvenirs avec ce lieu",
        //     "picture_date": "1990-02-08T14:00:00Z",
        //     "main_picture": "URL",
        // 		"additional_pictures": ["URL_image_1", "URL_image_2"]
        //   }
        // }
        $jsonContent = trim($jsonContent);
        $data = json_decode($jsonContent, true);
        
        // $data = [
        //     "user" => ["id" => 1],
        //     "location" => ["id" => 1],
        //     "place" => [
        //         "name" => "l'elysée",
        //         "type" => "batiment"
        //     ],
        //     "memory" => [
        //         "title" => "l'elysée en 1990",
        //         "content" => "que de souvenirs avec ce lieu",
        //         "picture_date" => "1990-02-08T14:00:00Z",
        //         "main_picture" => "URL"
        //     ]
        // ];
        $user = $userRepository->find($data['user']['id']);
        $location = $locationRepository->find($data['location']['id']);
        $memoryData = $data['memory'];
        // dd($memory);
        $newMemory = new Memory();
        $newMemory->setTitle($memory['title']);
        $newMemory->setContent($memory['content']);
        $newMemory->setPictureDate(new DateTime($memory['picture_date']));
        $newMemory->setMainPicture($memory['main_picture']);
        $newMemory->setUser($user);
        $newMemory->setLocation($location);

        $entityManager->persist($newMemory);
                   
        $placeData = $data['place'];
        $newPlace = new Place();
        $newPlace->setName($place['name']);
        $newPlace->setType($place['type']);

        // additional image management //
        // if (isset($memoryData['additional_pictures']) && is_array($memoryData['additional_pictures'])) {
        //     foreach ($memoryData['additional_pictures'] as $additionalPictureUrl) {
        //         $additionalPicture = new Picture();
        //         $additionalPicture->setPicture($additionalPictureUrl);
        //         $additionalPicture->setMemory($newMemory);
        //         $entityManager->persist($additionalPicture);
        //     }
        // }


        if($newPlace){
            $newPlace->setLocation($location);
            $entityManager->persist($newPlace);
        }


        $entityManager->flush();
        return $this->json(['message' => 'Souvenir créé'], Response::HTTP_CREATED);
    }
}
