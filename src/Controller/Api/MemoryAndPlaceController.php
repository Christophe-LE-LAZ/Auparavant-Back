<?php

namespace App\Controller\Api;

use DateTime;
use App\Entity\User;
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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MemoryAndPlaceController extends AbstractController
{
    

    /**
     * TODO : Update a memory by its id
     * 
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param LocationRepository $locationRepository
     * @param UserRepository $userRepository
     * @param PlaceRepository $placeRepository
     * @return Response
     * 
     */
    #[Route('/api/update/memory-and-place', methods: ['PUT'])]
    public function update(Request $request, EntityManagerInterface $entityManager, PlaceRepository $placeRepository, MemoryRepository $memoryRepository, PictureRepository $pictureRepository)
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
    
        $currentMemory = $memoryRepository->find($data['place']['id'])
            ->setTitle($memoryData['title'])
            ->setContent($memoryData['content'])
            ->setPictureDate(new DateTime($memoryData['picture_date']))
            ->setMainPicture($memoryData['main_picture'])
            ->setUpdatedAt(new DateTimeImmutable());
            // ->setPlace($place);

        $entityManager->persist($currentMemory);
                   

        // additional image management //
        foreach ($memoryData['additional_pictures'] as $additionalPictureData) {
            $additionalPictureId = isset($additionalPictureData['id']) ? $additionalPictureData['id'] : null;
        
            if ($additionalPictureId) {
                // Update an existing photo
                $additionalPicture = $pictureRepository->find($additionalPictureId);
                if ($additionalPicture) {
                    $additionalPicture
                        ->setPicture($additionalPictureData['URL_image'])
                        ->setMemory($currentMemory)
                        ->setUpdatedAt(new DateTimeImmutable());
        
                    $entityManager->persist($additionalPicture);
                }
            } else {
                // create new picture
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
}
