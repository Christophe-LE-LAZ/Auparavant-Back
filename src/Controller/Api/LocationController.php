<?php

namespace App\Controller\Api;


use DateTimeImmutable;
use App\Entity\Location;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;


class LocationController extends AbstractController
{
    /**
     * Display all locations
     
     * @param LocationRepository $locationRepository
     * @return Response
     */
    #[Route('/api/locations', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the location list',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Location::class, groups: ['get_location'])),
            example: [
                [
                    "id" => 1,
                    "area" => "Île-de-France",
                    "department" => "Paris",
                    "district" => "Quartier latin",
                    "street" => "28 place du Panthéon",
                    "city" => "Paris",
                    "zipcode" => 75005,
                    "latitude" => "48.84619800",
                    "longitude" => "2.34610500"
                ],
                [
                    "id"=> 2,
                    "area"=> "Île-de-France",
                    "department"=> "Paris",
                    "district"=> "Gros-Caillou",
                    "street"=> "5 avenue Anatole-France",
                    "city"=> "Paris",
                    "zipcode"=> 75007,
                    "latitude"=> "48.85829600",
                    "longitude"=> "2.29447900"
                ],
                ]
    ))]
    #[OA\Tag(name: 'location')]
    public function index(LocationRepository $locationRepository)
    {
        $locations = $locationRepository->findAll();

        return $this->json(
            $locations, 200, [], ['groups' => ['get_location']]);
    }

    /**
     * Display a single location by its id
     * @param Location $location
     * @return Response
     */
    #[Route('/api/location/{id<\d+>}', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns a single location',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Location::class, groups: ['get_location'])),
            example: [
                [
                    "id" => 1,
		            "area" => "Bretagne",
		            "department" => "Finistère",
		            "district" => "St Pierre",
		            "street" => "22 rue de Paul Éluard",
		            "city" => "Brest",
		            "zipcode" => 29200,
		            "latitude" => "48.39039400",
		            "longitude" => "-4.48607600"
                ] 
                ]
    ))]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "ID of the location",
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag(name: 'location')]
    public function read(Location $location = null )
    {
        if (!$location) {
            return $this->json(
                "Erreur : Localité inexistante", 404
            );
        }

        return $this->json($location, 200, [], ['groups' => ['get_location']]
    );
    }

    /**
     * Create a new location
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    #[Route('/api/secure/create/location', methods: ['POST'])]
    #[OA\Response(
        response: 201,
        description: 'Returns a single location',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Location::class, groups: ['get_location'])),
            example: [
                [
                    "id" => 1,
		            "area" => "Bretagne",
		            "department" => "Finistère",
		            "district" => "St Pierre",
		            "street" => "22 rue de Paul Éluard",
		            "city" => "Brest",
		            "zipcode" => 29200,
		            "latitude" => "48.39039400",
		            "longitude" => "-4.48607600"
                ] 
                ]
    ))]
    #[OA\RequestBody(new Model(type: Location::class, groups: ["location-create"]))]
 
    #[OA\Tag(name: 'location')]
    public function create(SerializerInterface $serializer, EntityManagerInterface $entityManager, Request $request)
    {
        $location = $serializer->deserialize($request->getContent(), Location::class, 'json');

        $entityManager->persist($location);
        $entityManager->flush();

        return $this->json($location, 201, [], ['groups' => ['get_location']]);
    }

    /**
     * Update a location by its id
     * @param Location $location
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/api/secure/update/location/{id<\d+>}', methods: ['PUT'])]
    public function update(Location $location = null, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        if(!$location) {
            return $this->json(
                "Erreur : La localité n'existe pas", 404
            );
        }
        $serializer->deserialize($request->getContent(), Location::class, 'json', ['object_to_populate'=>$location]);
        $location->setUpdatedAt(new DateTimeImmutable());

        $entityManager->flush();

        return $this->json($location, 200, [], ['groups' => ['get_location']]);
    }

    /**
     * Delete a location by its id
     */
    #[Route('/api/secure/delete/location/{id<\d+>}', methods: ['DELETE'])]
    public function delete(Location $location, EntityManagerInterface $entityManager): Response
    {
        if(!$location) {
            return $this->json(
                "Erreur : La localité n'existe pas", 404
            );
        }
        $entityManager->remove($location);
        $entityManager->flush();

        return $this->json(['message' => 'Localité supprimée'], Response::HTTP_OK);
    }
}
