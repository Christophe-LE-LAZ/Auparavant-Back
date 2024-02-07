<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods:['POST'])]
    public function apiLogin(Request $request, AuthenticationUtils $authenticationUtils): JsonResponse
    {
    
        $user = $this->getUser();

        // Handle authentication failure
        if (!$user) {
        return $this->json(
                "Error : Utilisateur inexistant", 404
            );
        }
        return $this->json($user, 200, [], ['groups' => ['get_user']]
    );
    }

}

?>