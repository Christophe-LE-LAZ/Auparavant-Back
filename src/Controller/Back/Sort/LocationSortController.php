<?php

namespace App\Controller\Back\Sort;

use App\Repository\LocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/back/sort/location')]
class LocationSortController extends AbstractController
{
    /**
     * Location sorting management
     *
     * @param Request $request
     * @param LocationRepository $locationRepository
     * @return void
     */
    #[Route('/', name: 'app_sort_location_by', methods: ['GET'])]
    public function sortBy(Request $request, LocationRepository $locationRepository) {

        $order = $request->query->get('order', '');
        $selectedDirection = $request->query->get('selectedDirection', 'asc');

        $direction = in_array($selectedDirection, ['asc', 'desc']) ? $selectedDirection : 'asc';

        $locations = $locationRepository->findBy([], [$order => $direction]);

        return $this->render('back/location/index.html.twig', [
            'locations' => $locations,
        ]);
    }
}
?>