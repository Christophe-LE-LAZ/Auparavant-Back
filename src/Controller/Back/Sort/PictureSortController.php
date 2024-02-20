<?php

namespace App\Controller\Back\Sort;

use App\Entity\Picture;
use App\Repository\PictureRepository;
use App\Entity\Memory;
use App\Repository\MemoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/back/sort/picture')]
class PictureSortController extends AbstractController
{
    #[Route('/', name: 'app_sort_picture_by', methods: ['GET'])]
    public function sortBy(Request $request,PictureRepository $pictureRepository, MemoryRepository $memoryRepository) {

        $order = $request->query->get('order', '');
        $selectedDirection = $request->query->get('selectedDirection', 'asc');

        $direction = in_array($selectedDirection, ['asc', 'desc']) ? $selectedDirection : 'asc';

        $sortResults = null;
        $pictures = null;

        if ($order === 'memory') {
            $sortResults = $pictureRepository->findByOrderAlphabeticalMemory($direction);
            // dd($sortResults);
        }

        $pictures = $pictureRepository->findBy([], [$order => $direction]);
        


        return $this->render('back/picture/index.html.twig', [
            'pictures' => $pictures,
            'order' => $order, 
            'sortResults' => $sortResults,
        ]);
    }
}
?>