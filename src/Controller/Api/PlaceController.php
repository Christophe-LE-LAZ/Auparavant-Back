<?php

namespace App\Controller\Api;

use App\Entity\Place;
use DateTimeImmutable;
use OpenApi\Attributes as OA;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * This controller groups together all the methods that manage places.
 * A first one displays all places.
 * A second one displays a single place by its id.
 * A third one adds a new place.
 * A fourth one updates a place by its id.
 * A fifth and last one deletes a place by its id.
 */
class PlaceController extends AbstractController
{
    /**
     * Display all places
     * @param PlaceRepository $placeRepository
     * @return Response
     */
    #[Route('/api/places', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the place list',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Place::class, groups: ['get_place'])),
            example: [
                [
                    "id" => 1,
                    "name" => "Le Panthéon",
                    "type" => "Mausolée"
                ],
                [
                    "id" => 2,
                    "name" => "Tour Eiffel",
                    "type" => "Tour autoportante"
                ],
                ]
    ))]
    #[OA\Tag(name: 'place')]
    public function index(PlaceRepository $placeRepository)
    {
        $places = $placeRepository->findAll();

        return $this->json($places, 200, [], ['groups' => ['get_place']]);
    }

    /**
     * Display a single place by its id
     * @param Place $place
     * @return Response
     */
    #[Route('/api/place/{id<\d+>}', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns a single place',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Place::class, groups: ['get_place'])),
            example: [
                [
                    "id" => 1,
                    "name" => "Le Panthéon",
                    "type" => "Mausolée"
                ]
                ]
    ))]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "ID of the place",
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag(name: 'place')]
    public function read(Place $place = null )
    {
        if (!$place) {
            return $this->json(
                "Erreur : Endroit inexistant", 404
            );
        }

        return $this->json($place, 200, [], ['groups' => ['get_place']]
    );
    }

    /** 
     * Create a new place
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    #[Route('/api/secure/create/place', methods: ['POST'])]
    #[OA\Tag(name: 'hidden')]
    public function create(SerializerInterface $serializer, EntityManagerInterface $entityManager, Request $request)
    {
        $place = $serializer->deserialize($request->getContent(), Place::class, 'json');

        $entityManager->persist($place);
        $entityManager->flush();

        return $this->json($place, 201, []);
    }

    /**
     * Update a place by its id
     * @param Place $place
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/api/secure/update/place/{id<\d+>}', methods: ['PUT'])]
    #[OA\Tag(name: 'hidden')]
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

    /**
     * Delete a place by its id
     * 
     * @param Place $place
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/api/secure/delete/place/{id<\d+>}', methods: ['DELETE'])]
    #[OA\Response(
        response: 200,
        description: 'Deletes a place',
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "ID of the place",
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag(name: 'hidden')]
    public function delete(Place $place, EntityManagerInterface $entityManager): Response
    {
        if(!$place) {
            return $this->json(
                "Erreur : L'endroit n'existe pas", 404
            );
        }
        $entityManager->remove($place);
        $entityManager->flush();

        return new Response('Endroit supprimé', 200);
    }
}