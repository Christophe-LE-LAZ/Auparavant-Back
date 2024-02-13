<?php

namespace App\Controller\Api;

use App\Entity\User;
use OpenApi\Attributes as OA;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
     /**
     * Create a new user
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    #[OA\RequestBody(  
        description: 'Exemple of data to be supplied to create the user',    
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'firstname', type:'string', example:'John'),
                new OA\Property(property: 'lastname', type:'string', example:'Doe'),
                new OA\Property(property: 'email', type:'string', example:'john.doe@email.com'),
                new OA\Property(property: 'password', type:'string', example:'pupuce'),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Returns a newly created user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class, groups: ['get_user'])),
            example: [
                [
                    "id" => 1,
                    "firstname" => "John",
                    "lastname" => "Doe",
                    "email" => "john.doe@email.com",
                ] 
                ]
    ))]
    #[OA\Tag(name: 'registration')]
    public function registerApi(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        if ($existingUser) {
            return $this->json(['error' => 'L\'email est déjà utilisé par un autre utilisateur'], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        $user->setEmail($data['email']);
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $data['password']
            )
        );

        $entityManager->persist($user);
        $entityManager->flush();
        return $this->json(['user' => $user, 'message' => 'Utilisateur enregistré'], Response::HTTP_CREATED, [], ['groups' => ['get_user']]);
    }
}