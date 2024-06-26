<?php

namespace App\Controller\Api;

use DateTime;
use App\Entity\Place;
use App\Entity\Memory;
use DateTimeImmutable;
use App\Entity\Location;
use App\Service\FileUploader;
use OpenApi\Attributes as OA;
use App\Service\MemoryProcessor;
use App\Repository\UserRepository;
use App\Repository\PlaceRepository;
use App\Repository\MemoryRepository;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * This controller groups together all the methods that manage memories.
 * One method displays all memories.
 * One displays a single memory.
 * One displays the last and latest three memories created.
 * One displays two random memory pictures: The pictures of an old and a recent memory of a place with the same address. Before/after pictures.
 * Two methods create a memory:
 * -> The first one creates a memory from an existing locality and creates the name and type of the place if the existing ones are not suitable for this memory.
 * -> The second one creates a memory and a new locality as well as the name and type of the corresponding place.
 * Another one updates a memory with its identifier by adding, modifying or deleting additional photos.
 * A final one deletes a memory by its identifier and the data assigned to it.
 */
class MemoryController extends AbstractController
{

    private $fileUploader;
    private $memoryRepository;
    private $memoryProcessor;

    public function __construct(FileUploader $fileUploader, MemoryRepository $memoryRepository, MemoryProcessor $memoryProcessor)
    {
        $this->fileUploader = $fileUploader;
        $this->memoryRepository = $memoryRepository;
        $this->memoryProcessor = $memoryProcessor;
    }


    /**
     * Display all memories
     * 
     * @param MemoryRepository $memoryRepository
     * @return Response
     */
    #[Route('/api/memories', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the memory list',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Memory::class, groups: ['get_memory', 'get_location', 'get_place', 'get_user', 'get_picture'])),
            example: [
                [
                    "id" => 1,
                    "title" => "Le Panthéon en 1792",
                    "content" => "Le Panthéon en 1792, avec La Renommée en son sommet.n",
                    "picture_date" => "1792-01-01T00:00:00+00:00",
                    "main_picture" => "fileName.jpg",
                    "location" => [
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
                    "picture" => [
                        [
                        "id" => 1,
                        "picture" => "filename.jpg",
                        ],
                        [
                            "id" => 2,
                            "picture" => "filename.jpg",
                            ],
                       
                        ],
                    "user" => [
                        "id" => 1,
                        "firstname" => "Aurélien",
                        "lastname" => "ROUCHETTE-MARET",
                        "email" => "aurelien.rouchette@orange.fr",
                        "roles" => [
                            "ROLE_USER",
                            "ROLE_ADMIN"
                        ]
                    ],
                    "place" => [
                        "id" => 1,
                        "name" => "Le Panthéon",
                        "type" => "Mausolée"
                    ]
                ],
                [
                    "id" => 2,
                    "title" => "Le Panthéon de nos jours",
                    "content" => "Le Panthéon vu de la tour Montparnasse en 2016.",
                    "picture_date" => "2016-01-01T00:00:00+00:00",
                    "main_picture" => "fileName.jpg",
                    "location" => [
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
                    "picture" => [
                        [
                        "id" => 1,
                        "picture" => "filename.jpg",
                        ],
                        [
                            "id" => 2,
                            "picture" => "filename.jpg",
                            ],
                       
                        ],
                    "user" => [
                        "id" => 1,
                        "firstname" => "Aurélien",
                        "lastname" => "ROUCHETTE-MARET",
                        "email" => "aurelien.rouchette@orange.fr",
                        "roles" => [
                            "ROLE_USER",
                            "ROLE_ADMIN"
                        ]
                    ],
                    "place" => [
                        "id" => 1,
                        "name" => "Le Panthéon",
                        "type" => "Mausolée"
                    ]
                ],
            ]
        )
    )]
    #[OA\Tag(name: 'memory')]
    public function index(MemoryRepository $memoryRepository)
    {
        $memories = $memoryRepository->findAll();
      
        return $this->json($memories, 200, [], ['groups' => ['get_memory', 'get_location', 'get_place', 'get_user', 'get_picture']]);
    }

     /**
     * Route qui permet de récupérer l'ensemble des souvenirs associés avec une image avant/après
     */
    #[Route('/api/memorieswithtwopictures', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the memory list with two pictures after/before',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Memory::class, groups: ['get_memory', 'get_location', 'get_place', 'get_user', 'get_picture'])),
            example: [
                [
                    "id" => 1,
                    "title" => "Le Panthéon en 1792",
                    "content" => "Le Panthéon en 1792, avec La Renommée en son sommet.n",
                    "picture_date" => "1792-01-01T00:00:00+00:00",
                    "main_picture" => "fileName.jpg",
                    "compare_picture" => "fileName.jpg",
                    "location" => [
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
                    "picture" => [
                        [
                        "id" => 1,
                        "picture" => "filename.jpg",
                        ],
                        [
                            "id" => 2,
                            "picture" => "filename.jpg",
                            ],
                       
                        ],
                    "user" => [
                        "id" => 1,
                        "firstname" => "Aurélien",
                        "lastname" => "ROUCHETTE-MARET",
                        "email" => "aurelien.rouchette@orange.fr",
                        "roles" => [
                            "ROLE_USER",
                            "ROLE_ADMIN"
                        ]
                    ],
                    "place" => [
                        "id" => 1,
                        "name" => "Le Panthéon",
                        "type" => "Mausolée"
                    ]
                ],
                [
                    "id" => 2,
                    "title" => "Le Panthéon de nos jours",
                    "content" => "Le Panthéon vu de la tour Montparnasse en 2016.",
                    "picture_date" => "2016-01-01T00:00:00+00:00",
                    "main_picture" => "fileName.jpg",
                    "compare_picture" => "fileName.jpg",
                    "location" => [
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
                    "picture" => [
                        [
                        "id" => 1,
                        "picture" => "filename.jpg",
                        ],
                        [
                            "id" => 2,
                            "picture" => "filename.jpg",
                            ],
                       
                        ],
                    "user" => [
                        "id" => 1,
                        "firstname" => "Aurélien",
                        "lastname" => "ROUCHETTE-MARET",
                        "email" => "aurelien.rouchette@orange.fr",
                        "roles" => [
                            "ROLE_USER",
                            "ROLE_ADMIN"
                        ]
                    ],
                    "place" => [
                        "id" => 1,
                        "name" => "Le Panthéon",
                        "type" => "Mausolée"
                    ]
                ],
            ]
        )
    )]
    #[OA\Tag(name: 'memory')]
    public function getAllMemoriesWithPicturesBeforeAfter(MemoryRepository $memoryRepository)
    {
        $memories = $memoryRepository->findAll();
           // Associer la compare_picture selon la date
        $processedMemories = $this->memoryProcessor->processMemories($memories);

         return $this->json($processedMemories, Response::HTTP_OK);
    }

    /**
     * Display a single memory by its id
     * 
     * @param Memory $memory
     * @return Response
     */
    #[Route('/api/memory/{id<\d+>}', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns a single memory',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Memory::class, groups: ['get_memory', 'get_location', 'get_place', 'get_user', 'get_picture'])),
            example: [
                [
                    "id" => 1,
                    "title" => "Le Panthéon en 1792",
                    "content" => "Le Panthéon en 1792, avec La Renommée en son sommet.n",
                    "picture_date" => "1792-01-01T00:00:00+00:00",
                    "main_picture" => "fileName.jpg",
                    "location" => [
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
                    "picture" => [
                        [
                        "id" => 1,
                        "picture" => "filename.jpg",
                        ],
                        [
                            "id" => 2,
                            "picture" => "filename.jpg",
                            ],
                       
                        ],
                    "user" => [
                        "id" => 1,
                        "firstname" => "Aurélien",
                        "lastname" => "ROUCHETTE-MARET",
                        "email" => "aurelien.rouchette@orange.fr",
                        "roles" => [
                            "ROLE_USER",
                            "ROLE_ADMIN"
                        ]
                    ],
                    "place" => [
                        "id" => 1,
                        "name" => "Le Panthéon",
                        "type" => "Mausolée"
                    ],
                ]
            ]
        )
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "ID of the memory",
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag(name: 'memory')]
    public function read(Memory $memory = null)
    {
        if (!$memory) {
            return $this->json(
                "Erreur : Souvenir inexistant",
                404
            );
        }

        return $this->json(
            $memory,
            200,
            [],
            ['groups' => ['get_memory', 'get_location', 'get_place', 'get_user', 'get_picture']]
        );
    }

    /**
     * Display the latest three memories by creation date
     *
     * @param MemoryRepository $memoryRepository
     * @return Response
     */
    #[Route('/api/memories/latest', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the latest three memories',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Memory::class, groups: ['get_memory', 'get_location', 'get_place', 'get_user', 'get_picture'])),
            example: [
                [
                    "id" => 8,
                    "title" => "Quartier Clause",
                    "content" => "Projet d'aménagement urbain en 2023",
                    "picture_date" => "2024-01-01T00:00:00+00:00",
                    "main_picture" => "fileName.jpg",
                    "location" => [
                        "id" => 5,
                        "area" => "Île-de-France",
                        "department" => "Essonne",
                        "district" => "Clause",
                        "street" => "Impasse du Blutin",
                        "city" => "Brétigny",
                        "zipcode" => 91220,
                        "latitude" => "48.60870000",
                        "longitude" => "2.30685000"
                    ],
                    "picture" => [
                        [
                        "id" => 1,
                        "picture" => "filename.jpg",
                        ],
                        [
                            "id" => 2,
                            "picture" => "filename.jpg",
                            ],
                       
                        ],
                    "user" => [
                        "id" => 1,
                        "firstname" => "Aurélien",
                        "lastname" => "ROUCHETTE-MARET",
                        "email" => "aurelien.rouchette@orange.fr",
                        "roles" => [
                            "ROLE_USER",
                            "ROLE_ADMIN"
                        ]
                    ],
                    "place" => [
                        "id" => 6,
                        "name" => "Résidence Clause",
                        "type" => "Résidence d'immeubles"
                    ]
                ],
                [
                    "id" => 7,
                    "title" => "Propriété de M. Clause",
                    "content" => "Propriété de M. Clause, édifiée en 1912",
                    "picture_date" => "1912-01-01T00:00:00+00:00",
                    "main_picture" => "fileName.jpg",
                    "location" => [
                        "id" => 5,
                        "area" => "Île-de-France",
                        "department" => "Essonne",
                        "district" => "Clause",
                        "street" => "Impasse du Blutin",
                        "city" => "Brétigny",
                        "zipcode" => 91220,
                        "latitude" => "48.60870000",
                        "longitude" => "2.30685000"
                    ],
                    "picture" => [
                        [
                        "id" => 1,
                        "picture" => "filename.jpg",
                        ],
                        [
                            "id" => 2,
                            "picture" => "filename.jpg",
                            ],
                       
                        ],
                    "user" => [
                        "id" => 1,
                        "firstname" => "Aurélien",
                        "lastname" => "ROUCHETTE-MARET",
                        "email" => "aurelien.rouchette@orange.fr",
                        "roles" => [
                            "ROLE_USER",
                            "ROLE_ADMIN"
                        ]
                    ],
                    "place" => [
                        "id" => 5,
                        "name" => "Propriété Clause",
                        "type" => "Propriété"
                    ]
                ],
                [
                    "id" => 6,
                    "title" => "Incendie de Notre-Dame",
                    "content" => "L’incendie de Notre-Dame de Paris est un incendie majeur survenu à la cathédrale Notre-Dame de Paris, les 15 et 16 avril 2019, pendant près de 15 heures.",
                    "picture_date" => "2019-04-15T00:00:00+00:00",
                    "main_picture" => "fileName.jpg",
                    "location" => [
                        "id" => 3,
                        "area" => "Île-de-France",
                        "department" => "Paris",
                        "district" => "Notre-Dame",
                        "street" => "6 Parvis Notre-Dame - Place Jean-Paul II",
                        "city" => "Paris",
                        "zipcode" => 75004,
                        "latitude" => "48.51110000",
                        "longitude" => "2.20590000"
                    ],
                    "picture" => [
                        [
                        "id" => 1,
                        "picture" => "filename.jpg",
                        ],
                        [
                            "id" => 2,
                            "picture" => "filename.jpg",
                            ],
                       
                        ],
                    "user" => [
                        "id" => 1,
                        "firstname" => "Aurélien",
                        "lastname" => "ROUCHETTE-MARET",
                        "email" => "aurelien.rouchette@orange.fr",
                        "roles" => [
                            "ROLE_USER",
                            "ROLE_ADMIN"
                        ]
                    ],
                    "place" => [
                        "id" => 3,
                        "name" => "Notre-Dame de Paris",
                        "type" => "Cathédrale"
                    ]
                ]
            ]
        )
    )]
    #[OA\Tag(name: 'memory')]
    public function latest(MemoryRepository $memoryRepository): Response
    {
        $latestMemories = $memoryRepository->findTheLatestOnes();

        if (!$latestMemories) {
            return $this->json(
                "Erreur : Données introuvables",
                404
            );
        }

        return $this->json(
            $latestMemories,
            200,
            [],
            ['groups' => ['get_memory', 'get_location', 'get_place', 'get_user', 'get_picture']]
        );
    }

    /**
     * Display two random memory pictures (Before/after pictures)
     *
     * @param MemoryRepository $memoryRepository
     * @param
     * @return Response
     */
    #[Route('api/memories/random', methods: ['GET'])]
    public function random(MemoryRepository $memoryRepository): Response
    {
        $pictures = $memoryRepository->findTwoRandomMemoryPictures();

        if (!$pictures) {
            return $this->json(
                "Erreur : Données introuvables",
                404
            );
        }

        return $this->json(
            $pictures,
            200,
            [],
            ['groups' => ['get_picture']]
        );
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
    #[OA\RequestBody(
        description: 'Exemple of data to be supplied to create the memory and place',
        content: new OA\JsonContent(
            oneOf: [
                new OA\Schema(
                    properties: [
                        new OA\Property(property: 'location', type: 'object', properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                        ]),
                        new OA\Property(property: 'place', type: 'object', properties: [
                            new OA\Property(property: 'create_new_place', type: 'boolean', example: true),
                            new OA\Property(property: 'name', type: 'string', example: "l'élysée"),
                            new OA\Property(property: 'type', type: 'string', example: 'bâtiment'),
                        ]),
                        new OA\Property(property: 'memory', type: 'object', properties: [
                            new OA\Property(property: 'title', type: 'string', example: "l'élysée en 1990"),
                            new OA\Property(property: 'content', type: 'string', example: 'que de souvenirs avec ce lieu'),
                            new OA\Property(property: 'picture_date', type: 'string', format: 'date-time', example: '1990-02-08'),
                        ]),
                    ]
                ),
                new OA\Schema(
                    properties: [
                        new OA\Property(property: 'location', type: 'object', properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                        ]),
                        new OA\Property(property: 'place', type: 'object', properties: [
                            new OA\Property(property: 'create_new_place', type: 'boolean', example: false),
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                        ]),
                        new OA\Property(property: 'memory', type: 'object', properties: [
                            new OA\Property(property: 'title', type: 'string', example: "l'elysée en 1990"),
                            new OA\Property(property: 'content', type: 'string', example: 'que de souvenirs avec ce lieu'),
                            new OA\Property(property: 'picture_date', type: 'string', format: 'date-time', example: '1990-02-08T14:00:00Z'),
                        ]),
                    ]
                ),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Returns the memory created, the associated location, place and user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Memory::class, groups: ['get_memory', 'get_location', 'get_place', 'get_user', 'get_picture']))
        )
    )]
    #[OA\Tag(name: 'memory')]
    public function createMemoryAndPlace(Request $request, EntityManagerInterface $entityManager, LocationRepository $locationRepository, PlaceRepository $placeRepository)
    {
        $jsonContent = $request->getContent();

        $jsonContent = trim($jsonContent);
        $data = json_decode($jsonContent, true);

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

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
        } else {
            $place = $placeRepository->find($data['place']['id']);
        }

        $memoryData = $data['memory'];
     
        $newMemory = (new Memory())
            ->setTitle($memoryData['title'])
            ->setContent($memoryData['content'])
            ->setMainPicture('')
            ->setPictureDate(new DateTime($memoryData['picture_date']))
            ->setUser($user)
            ->setLocation($location)
            ->setPlace($place);

        $entityManager->persist($newMemory);
        $entityManager->flush();

        return $this->json(['memory' => $newMemory, 'message' => 'Souvenir créé'], Response::HTTP_CREATED, [], ['groups' => ['get_memory', 'get_location', 'get_place', 'get_user', 'get_picture']]);
    }

    /**
     * Second method for creating a memory
     * 
     * Create a new memory including name, type and location
     * 
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @return Response
     * 
     */
    #[Route('/api/secure/create/memory-and-location-and-place', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'Exemple of data to be supplied to create the memory and place',
        content: new OA\JsonContent(

            properties: [
                new OA\Property(property: 'memory', type: 'object', properties: [
                    new OA\Property(property: 'title', type: 'string', example: "l'elysée en 1990"),
                    new OA\Property(property: 'content', type: 'string', example: 'que de souvenirs avec ce lieu'),
                    new OA\Property(property: 'picture_date', type: 'string', format: 'date-time', example: '1990-02-08T14:00:00Z'),
                ]),
                new OA\Property(property: 'place', type: 'object', properties: [
                    new OA\Property(property: 'name', type: 'string', example: "l'elysée"),
                    new OA\Property(property: 'type', type: 'string', example: 'bâtiment'),
                ]),
                new OA\Property(
                    property: 'location',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'area', type: 'string', example: 'Île-de-France'),
                        new OA\Property(property: 'department', type: 'string', example: 'Paris'),
                        new OA\Property(property: 'district', type: 'string', example: 'Quartier latin',  nullable: true),
                        new OA\Property(property: 'street', type: 'string', example: '28 place du Panthéon'),
                        new OA\Property(property: 'city', type: 'string', example: 'Paris'),
                        new OA\Property(property: 'zipcode', type: 'integer', example: 75005),
                        new OA\Property(property: 'latitude', type: 'string', example: '48.84619800'),
                        new OA\Property(property: 'longitude', type: 'string', example: '2.34610500'),
                    ]
                ),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Returns the memory created, the associated location, place and user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Memory::class, groups: ['get_memory', 'get_location', 'get_place', 'get_user', 'get_picture']))
        )
    )]
    #[OA\Tag(name: 'memory')]
    public function createMemoryAndLocationAndPlace(Request $request, EntityManagerInterface $entityManager)
    {
        $jsonContent = $request->getContent();
        $jsonContent = trim($jsonContent);
        $data = json_decode($jsonContent, true);

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $locationData = $data['location'];
        $newLocation = (new Location())
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
        
        $newMemory = (new Memory())
            ->setTitle($memoryData['title'])
            ->setContent($memoryData['content'])
            ->setMainPicture('')
            ->setPictureDate(new DateTime($memoryData['picture_date']))
            ->setUser($user)
            ->setPlace($newPlace)
            ->setLocation($newLocation);


        $entityManager->persist($newMemory);
        $entityManager->flush();

        return $this->json(['memory' => $newMemory, 'message' => 'Souvenir créé'], Response::HTTP_CREATED, [], ['groups' => ['get_memory', 'get_location', 'get_place', 'get_user', 'get_picture']]);
    }

    /**
     * Update a memory by its id
     * Only accessible to the user who created the memory
     * 
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param PlaceRepository $placeRepository
     * @param MemoryRepository $memoryRepository
     * @return Response
     * 
     */
    #[Route('/api/secure/update/memory-and-place/{id<\d+>}', methods: ['PUT'])]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "ID of the memory",
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        description: 'Example of data to be supplied to update the memory and place',
        content: new OA\JsonContent(
            oneOf: [
                new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'place',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'update_place', type: 'boolean', example: true),
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'new name'),
                                new OA\Property(property: 'type', type: 'string', example: 'new type'),
                            ]
                        ),
                        new OA\Property(
                            property: 'memory',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 7),
                                new OA\Property(property: 'title', type: 'string', example: 'new title'),
                                new OA\Property(property: 'content', type: 'string', example: 'new content'),
                                new OA\Property(property: 'picture_date', type: 'string', format: 'date-time', example: '1890-02-08T14:00:00Z'),
                            ]
                        ),
                    ]
                ),
                new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'place',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'update_place', type: 'boolean', example: false),
                            ]
                        ),
                        new OA\Property(
                            property: 'memory',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 7),
                                new OA\Property(property: 'title', type: 'string', example: 'l\'elysée en 1990'),
                                new OA\Property(property: 'content', type: 'string', example: 'que de souvenirs avec ce lieu'),
                                new OA\Property(property: 'picture_date', type: 'string', format: 'date-time', example: '1990-02-08T14:00:00Z'),
                            ]
                        ),
                    ]
                ),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns the updated memory, associated location, place and user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Memory::class, groups: ['get_memory', 'get_location', 'get_place', 'get_user', 'get_picture']))
        )
    )]
    #[OA\Tag(name: 'memory')]
    public function updateMemoryAndPlace(Request $request, EntityManagerInterface $entityManager, PlaceRepository $placeRepository, MemoryRepository $memoryRepository)
    {
        $jsonContent = $request->getContent();
        $jsonContent = trim($jsonContent);
        $data = json_decode($jsonContent, true);

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $memoryId = $data['memory']['id'];
        $currentMemory = $memoryRepository->find($memoryId);
        
        if ($user !== $currentMemory->getUser()) {
            return $this->json("Erreur : Vous n'êtes pas autorisé à modifier ce contenu.", 401);
        }

        $placeData = $data['place'];
        if ($placeData['update_place'] == true) {
            $currentPlace = $placeRepository->find($data['place']['id'])
                ->setName($placeData['name'])
                ->setType($placeData['type'])
                ->setUpdatedAt(new DateTimeImmutable());
            $entityManager->persist($currentPlace);
            $entityManager->flush();
        }
        else {

            $currentPlace = $placeRepository->find($currentMemory->getPlace());
        }

        $memoryData = $data['memory'];
        $currentMemory 
            ->setTitle($memoryData['title'])
            ->setContent($memoryData['content'])
            ->setMainPicture($currentMemory->getMainPicture())
            ->setPictureDate(new DateTime($memoryData['picture_date']))
            ->setUpdatedAt(new DateTimeImmutable())
            ->setPlace($currentPlace);

        $entityManager->persist($currentMemory);
        $entityManager->flush();
        return $this->json(['memory' => $currentMemory, 'message' => 'Souvenir mis à jour'], Response::HTTP_OK, [], ['groups' => ['get_memory', 'get_location', 'get_place', 'get_user', 'get_picture']]);
    }

    /**
     * Delete a memory by its id
     * Only accessible to the user who created the memory
     * 
     * @param Memory $memory
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/api/secure/delete/memory/{id<\d+>}', methods: ['DELETE'])]
    #[OA\Response(
        response: 200,
        description: 'Deletes a memory',
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        description: "ID of the memory",
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Tag(name: 'memory')]
    public function delete(Memory $memory, EntityManagerInterface $entityManager, ParameterBagInterface $params): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if ($user !== $memory->getUser()) {
            return $this->json("Erreur : Vous n'êtes pas autorisé à supprimer ce contenu.", 401);
        }

        if (!$memory) {
            return $this->json(
                "Erreur : Le souvenir n'existe pas",
                404
            );
        }

        


        // Delete the main picture
        $deleteFileMainPictureResult = $this->fileUploader->deletePictureFile($params->get('images_directory'), $memory->getMainPicture());
        if (!$deleteFileMainPictureResult) {
            return $this->json("Erreur : Échec de suppression de la photo", 500);
        }
        
        // Delete the additional picture(s)
        $deleteFilesAdditionalPictures = $memory->getPicture();
        if ($deleteFilesAdditionalPictures !== null) {
            foreach ($deleteFilesAdditionalPictures as $additionalPicture) {
                $deleteFilesAdditionalPictureResult = $this->fileUploader->deletePictureFile($params->get('images_directory'), $additionalPicture->getPicture());
                if (!$deleteFilesAdditionalPictureResult) {
                    return $this->json("Erreur : Échec de suppression de la photo", 500);
                }
            }
        }
        $entityManager->remove($memory);
        $entityManager->flush();

        // Retrieve the locality associated with the memory
        $location = $memory->getLocation();

       // Delete the locality if there are no other associated memories
        if ($location && $location->getMemories()->isEmpty()) {
        $entityManager->remove($location);
        $entityManager->flush();

        }

        // Retrieve the place associated with the location
        $place = $memory->getPlace();
        // Delete location if there are no other associated localities
        if ($place && $place->getMemories()->isEmpty()) {
            $entityManager->remove($place);
            $entityManager->flush();
        }


        return $this->json(['message' => 'Souvenir supprimé'], Response::HTTP_OK);
    }

   /**
     * Create a new memory
     * 
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    #[Route('hidden/api/secure/create/memory', methods: ['POST'])]
    #[OA\Tag(name: 'hidden')]
    public function create(SerializerInterface $serializer, EntityManagerInterface $entityManager, Request $request)
    {
        $memory = $serializer->deserialize($request->getContent(), Memory::class, 'json');

        $entityManager->persist($memory);
        $entityManager->flush();

        return $this->json($memory, 201, []);
    }

     /**
     * Update a memory by its id
     * 
     * @param Memory $memory
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('hidden/api/secure/update/memory/{id<\d+>}', methods: ['PUT'])]
    #[OA\Tag(name: 'hidden')]
    public function update(Memory $memory = null, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        if (!$memory) {
            return $this->json(
                "Erreur : Le souvenir n'existe pas",
                404
            );
        }
        $serializer->deserialize($request->getContent(), Memory::class, 'json', ['object_to_populate' => $memory]);
        $memory->setUpdatedAt(new DateTimeImmutable());

        $entityManager->flush();

        return $this->json($memory, 200, [], ['groups' => ['get_memory']]);
    }

  


}
