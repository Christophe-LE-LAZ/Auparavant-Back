<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class LoginController extends AbstractController
{
    #[Route('/api/login_check', name: 'api_login', methods:['POST'])]
//     public function apiLogin(Request $request, UserInterface $user): JsonResponse
//     {
//         // Handle authentication failure
//         if (!$user) {
//             return $this->json("Erreur : Utilisateur inexistant", 404);
//         }

//         return $this->json($user, 200, [], ['groups' => ['get_user']]);
//     }
// }


     public function apiLogin(#[CurrentUser] ?User $user): Response
      {
     if (null === $user) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $jwtManager->create($user);

          return $this->json([
            'user'  => $user->getUserIdentifier(),
            'token' => $token,
          ]);
      }
  

  }

?>