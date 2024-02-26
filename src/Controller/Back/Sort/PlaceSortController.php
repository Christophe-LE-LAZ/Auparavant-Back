<?php

namespace App\Controller\Back\Sort;

use App\Entity\Place;
use App\Repository\PlaceRepository;
use App\Entity\Location;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/back/sort/place')]
class PlaceSortController extends AbstractController
{
    /**
     * Place sorting management
     *
     * @param Request $request
     * @param LocationRepository $locationRepository
     * @param PlaceRepository $placeRepository
     * @return void
     */
    #[Route('/', name: 'app_sort_place_by', methods: ['GET'])]
    public function sortBy(Request $request, LocationRepository $locationRepository, PlaceRepository $placeRepository) {

        $order = $request->query->get('order', '');
        $selectedDirection = $request->query->get('selectedDirection', 'asc');

        $direction = in_array($selectedDirection, ['asc', 'desc']) ? $selectedDirection : 'asc';

        $sortResults = null;
        $places = null;

        if ($order === 'location') {
            $sortResults = $locationRepository->findByOrderAlphabeticalStreet($direction);
           
        }
           $places = $placeRepository->findBy([], [$order => $direction]);


        return $this->render('back/place/index.html.twig', [
            'places' => $places,
            'order' => $order, 
            'sortResults' => $sortResults,
        ]);
    }
}
?>