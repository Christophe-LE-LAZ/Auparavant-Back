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
use App\Repository\PlaceRepository;

#[Route('/back/sort/memory')]
class MemorySortController extends AbstractController
{
    #[Route('/', name: 'app_sort_memory_by', methods: ['GET'])]
    public function sortBy(Request $request, MemoryRepository $memoryRepository, LocationRepository $locationRepository, PlaceRepository $placeRepository) {

        $order = $request->query->get('order', '');
        $selectedDirection = $request->query->get('selectedDirection', 'asc');

        $direction = in_array($selectedDirection, ['asc', 'desc']) ? $selectedDirection : 'asc';

        $sortResults = null;
        $memories = null;

        if ($order === 'location') {
            $sortResults = $locationRepository->findByOrderAlphabeticalStreet($direction);
            //   foreach ($sortResults as $sort ) {
            //     dump ($sort);
            //        dd ($sort['id']);
            // }
        }

        if ($order === 'place') {
            $sortResults = $placeRepository->findByOrderAlphabeticalPlace($direction);
            foreach ($sortResults as $sort ) {
                   dump($sort['id']);   
            }
        }

        $memories = $memoryRepository->findBy([], [$order => $direction]);


        return $this->render('back/memory/index.html.twig', [
            'memories' => $memories,
            'order' => $order, 
            'sortResults' => $sortResults,
        ]);
    }
}
?>