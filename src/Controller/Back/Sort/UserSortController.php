<?php

namespace App\Controller\Back\Sort;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/back/sort/user')]
class UserSortController extends AbstractController
{
    /**
     * User sorting management
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @return void
     */
    #[Route('/', name: 'app_sort_user_by', methods: ['GET'])]
    public function sortBy(Request $request, UserRepository $userRepository) {

        $order = $request->query->get('order', '');
        $selectedDirection = $request->query->get('selectedDirection', 'asc');

        $direction = in_array($selectedDirection, ['asc', 'desc']) ? $selectedDirection : 'asc';

        $users = $userRepository->findBy([], [$order => $direction]);

        return $this->render('back/user/index.html.twig', [
            'users' => $users,
        ]);
    }
}
?>