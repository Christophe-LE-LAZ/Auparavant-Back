<?php

namespace App\Controller\Api;

use App\Entity\User;
use DateTimeImmutable;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * Display all users
     * @param UserRepository $userRepository
     * @return Response
     */
    #[Route('/api/users', methods: ['GET'])]
    public function index(UserRepository $userRepository)
    {
        // Fonction qui permet de lister tous les utilisateurs
        $users = $userRepository->findAll();

        return $this->json($users, 200, [], ['groups' => ['get_user']]);
    }

    /** 
     * Display a single user by its id
     * @param User $user
     * @return Response
     */
    #[Route('/api/user/{id<\d+>}', methods: ['GET'])]
    public function read(User $user = null )
    {
        // Si l'utilisateur n'existe pas, on renvoie un message et une erreur 404
        if (!$user) {
            return $this->json(
                "Erreur : Utilisateur inexistant", 404
            );
        }
        // Si il trouve un utilisateur avec l'id indique, il renvoie un code 200 et toutes les infos qu'il faut
        return $this->json($user, 200, [], ['groups' => ['get_user']]
    );
    }

    /**
     * Create a new user
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     */
    #[Route('/api/create/user', methods: ['POST'])]
    public function create(SerializerInterface $serializer, EntityManagerInterface $entityManager, Request $request)
    {
        // Pour la creation d'un utilisateur, on récupère des données en JSON, on envoie la requête en BDD et on sauvegarde. Si c'est OK, on indique un code 201
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($user, 201, []);
    }

    /**
     * Update a user by its id
     * @param User $user
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/api/update/user/{id<\d+>}', methods: ['PUT'])]
    public function update(User $user = null, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        // Pour la modification, on récupère l'id et on vérifie si il y a un utilisateur. Sinon, on affiche une erreur 404.
        if(!$user) {
            return $this->json(
                "Erreur : L'utilisateur n'existe pas", 404
            );
        }
        // Si l'utilisateur existe, on récupère et modifie les données en JSON, on envoie la requête et on sauvegarde en BDD avec un code 201.
        $serializer->deserialize($request->getContent(), User::class, 'json', ['object_to_populate'=>$user]);
        $user->setUpdatedAt(new DateTimeImmutable());

        $entityManager->flush();

        return $this->json($user, 200, []);
    }

    /**
     * Delete a user by its id
     */
    #[Route('/api/delete/user/{id<\d+>}', methods: ['DELETE'])]
    public function delete(User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return new Response('Utilisateur supprimé', 200);
    }
}