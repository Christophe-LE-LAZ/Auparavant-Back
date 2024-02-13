<?php

namespace App\Controller\Api;

use App\Entity\Picture;
use OpenApi\Attributes as OA;
use App\Repository\MemoryRepository;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
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
    #[OA\Response(
        response: 200,
        description: 'Returns the picture list',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Picture::class, groups: ['get_picture', 'get_memory_id'])),
            example: [
                [
                    "id" => 1,
                    "picture" => "https:\/\/upload.wikimedia.org\/wikipedia\/commons\/thumb\/c\/c9\/Dome_Panth%C3%A9on_Paris_10.jpg\/1280px-Dome_Panth%C3%A9on_Paris_10.jpg",
                    "memory" => [
                        "id" => 2
                    ]
                ],
                [
                    "id" => 2,
                    "picture" => "https:\/\/upload.wikimedia.org\/wikipedia\/commons\/thumb\/6\/67\/Dome_Panth%C3%A9on_Paris_16.jpg\/800px-Dome_Panth%C3%A9on_Paris_16.jpg",
                    "memory" => [
                        "id" => 2
                    ]
                ],
                ]
    ))]
    #[OA\Tag(name: 'picture')]
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
    #[OA\Response(
        response: 200,
        description: 'Returns a single picture',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Picture::class, groups: ['get_picture', 'get_memory_id'])),
            example: [
                [
                    "id" => 1,
                    "picture" => "https:\/\/upload.wikimedia.org\/wikipedia\/commons\/thumb\/c\/c9\/Dome_Panth%C3%A9on_Paris_10.jpg\/1280px-Dome_Panth%C3%A9on_Paris_10.jpg",
                    "memory" => [
                        "id" => 2
                    ]
                ] 
                ]
    ))]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "ID of the picture",
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag(name: 'picture')]
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
    #[Route('/api/secure/create/picture', methods: ['POST'])]
    #[OA\RequestBody(  
        description: 'Exemple of data to be supplied to create the picture',    
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'picture', type:'string', example:'photo.jpg'),
                new OA\Property(property:"memory", type:"object", properties:[
                new OA\Property(property:"id", type:"integer", example: 9)
            ]
        )]
    ))]
    #[OA\Response(
        response: 200,
        description: 'save the image associated with the memory')]
    #[OA\Tag(name: 'picture')]
    public function create(SerializerInterface $serializer, EntityManagerInterface $entityManager, Request $request, MemoryRepository $memoryRepository)
    {
        $jsonData = $request->getContent();
        $data = $serializer->decode($jsonData, 'json');
    
        $memoryId = $data['memory']['id'];
        $memory = $memoryRepository->find($memoryId);
        if (!$memory) {
            return $this->json("Erreur : Le souvenir associé n'existe pas", 404);
        }
        $picture = (new Picture())
        ->setMemory($memory)
        ->setPicture($data['picture']);

        $entityManager->persist($picture);
        $entityManager->flush();

        return $this->json(['message' => 'Photo créé'], Response::HTTP_CREATED);
    }

    /**
     * Update a picture by its id
     * @param Picture $picture
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param MemoryRepository $memoryRepository
     * @return Response
     */

    #[Route('/api/secure/update/picture/{id<\d+>}', methods: ['PUT'])]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "ID of the memory",
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(  
        description: 'Exemple of data to be supplied to update the picture',    
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'id', type:'integer', example:'20'),
                new OA\Property(property: 'picture', type:'string', example:'photo.jpg'),
                new OA\Property(property:"memory", type:"object", properties:[
                new OA\Property(property:"id", type:"integer", example: 9)
            ]
        )]
    ))]
    #[OA\Tag(name: 'picture')]
    public function update(Picture $picture, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, MemoryRepository $memoryRepository)
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
    #[Route('/api/secure/delete/picture/{id<\d+>}', methods: ['DELETE'])]
    #[OA\Response(
        response: 200,
        description: 'Deletes a picture',
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "ID of the picture",
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag(name: 'picture')]
    public function delete(Picture $picture, EntityManagerInterface $entityManager): Response
    {
        if(!$picture) {
            return $this->json(
                "Erreur : La photo n'existe pas", 404
            );
        }
        $entityManager->remove($picture);
        $entityManager->flush();

        return $this->json(['message' => 'Photo supprimée'], Response::HTTP_OK);
    }
}