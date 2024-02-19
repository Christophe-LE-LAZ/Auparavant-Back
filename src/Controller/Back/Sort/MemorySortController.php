<?php

namespace App\Controller\Back\Sort;

use App\Entity\Memory;
use App\Repository\MemoryRepository;
use App\Entity\Location;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/back/sort/memory')]
class MemorySortController extends AbstractController
{
    #[Route('/', name: 'app_sort_memory_by', methods: ['GET'])]
    public function sortBy(Request $request, MemoryRepository $memoryRepository, LocationRepository $locationRepository) {

        $order = $request->query->get('order', '');
        $selectedDirection = $request->query->get('selectedDirection', 'asc');

        $direction = in_array($selectedDirection, ['asc', 'desc']) ? $selectedDirection : 'asc';

        $streets = null;
        $memories = null;

        if ($order === 'location') {
            $streets = $locationRepository->findByOrderAlphabetical($direction);
        }

        $memories = $memoryRepository->findBy([], [$order => $direction]);


        return $this->render('back/memory/index.html.twig', [
            'memories' => $memories,
            'order' => $order, 
            'streets' => $streets,
        ]);
    }
}
?>