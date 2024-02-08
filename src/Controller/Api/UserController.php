<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    #[Route('/api/users', methods: ['GET'])]
    public function index(UserRepository $userRepository)
    {
        // Fonction qui permet de lister tous les utilisateurs
        $users = $userRepository->findAll();

        return $this->json($users, 200, [], ['groups' => ['get_user']]);
    }

    #[Route('/api/user/{id<\d+>}', methods: ['GET'])]
    public function read(User $user = null )
    {
        // Si l'utilisateur n'existe pas, on renvoie un message et une erreur 404
        if (!$user) {
            return $this->json(
                "Error : Utilisateur inexistant", 404
            );
        }
        // Si il trouve un utilisateur avec l'id indique, il renvoie un code 200 et toutes les infos qu'il faut
        return $this->json($user, 200, [], ['groups' => ['get_user']]
    );
    }

    #[Route('/api/create/user', methods: ['POST'])]
    public function create(SerializerInterface $serializer, EntityManagerInterface $entityManager, Request $request)
    {
        // Pour la creation d'un utilisateur, on recupere des donnees en JSON, on envoie la requete en BDD et on sauvegarde. Si c'est OK, on indique un code 201
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($user, 201, []);
    }

    #[Route('/api/update/user/{id<\d+>}', methods: ['PUT'])]
    public function update(User $user = null, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        // Pour la modification, on recupere l'id et on verifie si il y a un utilisateur. Si non, on affiche une erreur 404.
        if(!$user) {
            return $this->json(
                "Erreur : L'utilisateur n'existe pas", 404
            );
        }
        // Si l'utilisateur existe, on recupere et modifie les donnees en JSON, on envoie la requete et on sauvegarde en BDD avec un code 201.
        $serializer->deserialize($request->getContent(), User::class, 'json', ['object_to_populate'=>$user]);
        $user->setUpdatedAt(new DateTimeImmutable());

        $entityManager->flush();

        return $this->json($user, 200, []);
    }

    #[Route('/api/delete/user/{id<\d+>}', methods: ['DELETE'])]
    public function delete()
    {

    }
}