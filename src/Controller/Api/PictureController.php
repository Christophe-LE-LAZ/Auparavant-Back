<?php

namespace App\Controller\Api;

use App\Entity\Memory;
use App\Entity\Picture;
use OpenApi\Attributes as OA;
use App\Repository\PlaceRepository;
use App\Repository\MemoryRepository;
use App\Repository\PictureRepository;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * This controller groups together all the methods that manage pictures.
 * A first one displays all additional pictures.
 * A second one displays a single additional picture by its id.
 * A third one uploads or updates the main memory picture.
 * A fourth one uploads or updates (an) addditional memory picture(s).
 * A fifth and last one deletes a additional picture by its id.
 */
class PictureController extends AbstractController
{
    /**
     * Display all additional pictures
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
                    "picture" => "fileName.jpg",
                    "memory" => [
                        "id" => 2
                    ]
                ],
                [
                    "id" => 2,
                    "picture" => "fileName.jpg",
                    "memory" => [
                        "id" => 2
                    ]
                ],
            ]
        )
    )]
    #[OA\Tag(name: 'picture')]
    public function index(PictureRepository $pictureRepository)
    {
        $pictures = $pictureRepository->findAll();

        return $this->json($pictures, 200, [], ['groups' => ['get_picture', 'get_memory_id']]);
    }

    /**
     * Display a single additional picture by its id
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
                    "picture" => "fileName.jpg",
                    "memory" => [
                        "id" => 2
                    ]
                ]
            ]
        )
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "ID of the picture",
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag(name: 'picture')]
    public function read(Picture $picture = null)
    {
        if (!$picture) {
            return $this->json(
                "Erreur : Photo inexistante",
                404
            );
        }

        return $this->json(
            $picture,
            200,
            [],
            ['groups' => ['get_picture', 'get_memory_id']]
        );
    }

    /**
     * Upload or update the main memory picture
     * 
     * @param Memory $memory
     * @param Request $request
     * @param ParameterBagInterface $params
     * @param EntityManagerInterface $entityManager
     * @param LocationRepository $locationRepository
     * @param PlaceRepository $placeRepository
     * @return Response
     */
    #[Route('/api/secure/upload_update/main_picture/{id<\d+>}', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'Exemple of data to be supplied to upload or update the picture',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'picture', type: 'file', example: 'photo.jpg'),
                new OA\Property(
                    property: "memory",
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 9)
                    ]
                )
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Saves the image associated with the memory',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Memory::class, groups: ['get_memory', 'get_location', 'get_picture', 'get_place', 'get_user']))
        )
    )]
    #[OA\Tag(name: 'picture')]
    public function upload_update_main_picture(Memory $memory, Request $request, ParameterBagInterface $params, EntityManagerInterface $entityManager, LocationRepository $locationRepository, PlaceRepository $placeRepository)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($user !== $memory->getUser()) {
            return $this->json("Erreur : Vous n'êtes pas autorisé à ajouter de photo sur ce souvenir.", 401);
        }
        // Retrieve the uploaded picture file from the request
        $picture = $request->files->get('main_picture');

        if ($picture === null) {
            // Handle case where no picture is uploaded (possibly indicating removal of the main picture)

            // If the memory had a main picture, keep it and update the memory
            if ($memory->getMainPicture()) {
                $entityManager->flush();
                return $this->json([
                    'message' => 'Le souvenir a bien été mis à jour.'
                ]);
            }

            // Check if related entities (Location and Place) need to be removed
            $location = $memory->getLocation();
            $place = $memory->getPlace();

            $otherMemoriesWithLocation = $locationRepository->findMemoriesWithLocation($location, $memory->getId());
            $otherMemoriesWithPlace = $placeRepository->findMemoriesWithPlace($place, $memory->getId());

            if (empty($otherMemoriesWithLocation) && empty($otherMemoriesWithPlace)) {
                // Remove Location, Place, and Memory if there are no other relations
                $entityManager->remove($place);
                $entityManager->remove($location);
                $entityManager->remove($memory);
                $entityManager->flush();
            } elseif (empty($otherMemoriesWithLocation)) {
                // Remove Location if it doesn't have other relations
                $entityManager->remove($location);
                $entityManager->remove($memory);
                $entityManager->flush();
            } elseif (empty($otherMemoriesWithPlace)) {
                // Remove Place if it doesn't have other relations
                $entityManager->remove($place);
                $entityManager->remove($memory);
                $entityManager->flush();
            }

            return $this->json("Erreur : Le souvenir doit contenir une image principale  .", 400);
        }
        // Continue with the code to handle the case when a new picture is uploaded

        // on ajoute uniqid() afin de ne pas avoir 2 fichiers avec le même nom
        $newFilename = uniqid() . '.' . $picture->getClientOriginalExtension();

        // enregistrement de l'image dans le dossier public du serveur
        // params->get('public') =>  va chercher dans services.yaml la variable public
        $picture->move($params->get('images_directory'), $newFilename);

        // ne pas oublier d'ajouter l'url de l'image dans l'entité appropriée
        // $entity est l'entity qui doit recevoir votre image
        $memory->setMainPicture($newFilename);
        $entityManager->flush();


        return $this->json([
            'message' => 'Image téléchargée et associée au souvenir avec succès.'
        ]);
    }

    /**
     * Upload or update (an/the) additional memory picture(s)
     * 
     * @param
     * @return Response
     */
    #[Route('api/secure/upload/additional_pictures/{id<\d+>}', methods: ['POST'])]
    public function upload_update_additional_pictures(): Response
    {
        return $this->json([
            'message' => 'Image(s) supplémentaire(s) téléchargée(s) et associée(s) au souvenir avec succès.'
        ]);
    }

    /**
     * Delete an additional picture by its id
     * Only accessible to the user who created the memory
     * 
     * @param Picture $picture
     * @param EntityManagerInterface $entityManager
     * @param MemoryRepository $memoryRepository
     * @return Response
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
    public function delete(Picture $picture, EntityManagerInterface $entityManager, MemoryRepository $memoryRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $memoryId = $picture->getMemory();
        $memory = $memoryRepository->find($memoryId);
        if ($user !== $memory->getUser()) {
            return $this->json("Erreur : Vous n'êtes pas autorisé à supprimer cette photo.", 401);
        }

        if (!$picture) {
            return $this->json(
                "Erreur : La photo n'existe pas",
                404
            );
        }
        $entityManager->remove($picture);
        $entityManager->flush();

        return $this->json(['message' => 'Photo supprimée'], Response::HTTP_OK);
    }
}
