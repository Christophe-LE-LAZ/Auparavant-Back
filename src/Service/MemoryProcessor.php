<?php

// src/Service/FileUploader.php
namespace App\Service;

use App\Repository\MemoryRepository;


class MemoryProcessor
{
    private $memoryRepository;

    public function __construct(MemoryRepository $memoryRepository)
    {
        $this->memoryRepository = $memoryRepository;
    }

    /**
    * Boucle sur l'ensemble des souvenirs; récupère pour chaque localité la photo la plus récente et la plus ancienne.
    * Compare les dates de souvenir pour savoir si le souvenir et le plus récent ou non.
    * Si le souvenir est le plus récent alors lui associer la photo du souvenir le plus ancien de la localité;
    * Si le souvenir n'est pas le plus récent de la localité alors lui associer la photo la plus récente de la localité;
    */
    public function processMemories(array $memories): array
    {
         $processedMemories = [];
         foreach ($memories as $memory) {
        $locationId = $memory->getLocation()->getId();
    
        // Trouver la photo la plus récente pour cette localité
        $mostRecentMemory = $this->memoryRepository->findMostRecentMemoryByLocation($locationId);
        $mostRecentMainPicture = $mostRecentMemory['main_picture'];
          // Trouver la photo la plus ancienne pour cette localité
        $oldestMemory = $this->memoryRepository->findOldestMemoryByLocation($locationId);
        $oldestMainPicture = $oldestMemory['main_picture'];
         // Convertir les dates en objet DateTime
        $memoryDate = new \DateTime($memory->getPictureDate()->format('Y-m-d H:i:s'));
        $mostRecentDate = new \DateTime($mostRecentMemory['picture_date']);
       
        // Comparer les dates pour déterminer le plus récent ou le plus ancien
        if ($memoryDate < $mostRecentDate) {
           $comparePicture = $mostRecentMainPicture;
        } elseif ($memoryDate == $mostRecentDate) {
           $comparePicture = $oldestMainPicture;
        } else {
           $comparePicture = null;
        }
         // Itération sur chaque photo additionnelle associée au memory et stockage dans le tableau $pictures[]
        $pictures = [];
        foreach ($memory->getPicture() as $picture) {
            $pictures[] = [
                'id' => $picture->getId(),
                'picture' => $picture->getPicture(),
            ];
        }
          $processedMemories[] = [
           'id' => $memory->getId(),
           'title' => $memory->getTitle(),
           'content' => $memory->getContent(),
           'picture_date' => $memory->getPictureDate(),
           'main_picture' => $memory->getMainPicture(),
           'compare_picture' => $comparePicture,
           'location' => [
            'id' => $memory->getLocation()->getId(),
            'area' => $memory->getLocation()->getArea(),
            'department' => $memory->getLocation()->getDepartment(),
            'district' => $memory->getLocation()->getDistrict(),
            'street' => $memory->getLocation()->getStreet(),
            'city' => $memory->getLocation()->getCity(),
            'zipcode' => $memory->getLocation()->getZipcode(),
            'latitude' => $memory->getLocation()->getLatitude(),
            'longitude' => $memory->getLocation()->getLongitude(),],
            'picture' => $pictures,
           'user' => [
            'id' => $memory->getUser()->getId(),
            'firstname' => $memory->getUser()->getFirstName(),
            'lastname' => $memory->getUser()->getLastName(),
            'email' => $memory->getUser()->getEmail(),
            'roles' => $memory->getUser()->getRoles(),],
           'place' => [
             'id'=> $memory->getPlace()->getId(),
             'name'=> $memory->getPlace()->getName(),
             'type'=> $memory->getPlace()->getType(),
           ],
           ];
        }
     return $processedMemories;
    }
}