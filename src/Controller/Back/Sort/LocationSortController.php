<?php

namespace App\Controller\Back\Sort;

use App\Entity\Location;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/back/sort/location')]
class LocationSortController extends AbstractController
{
    #[Route('/', name: 'app_sort_location_by', methods: ['GET'])]
    public function sortBy(Request $request, LocationRepository $locationRepository) {

        $order = $request->query->get('order', 'id');
        $selectedDirection = $request->query->get('selectedDirection', 'asc');

        $direction = in_array($selectedDirection, ['asc', 'desc']) ? $selectedDirection : 'asc';

        $locations = $locationRepository->findBy([], [$order => $direction]);

        return $this->render('back/location/index.html.twig', [
            'locations' => $locations,
        ]);
    }
}
?>