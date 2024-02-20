<?php

namespace App\Controller\Back;

use App\Entity\Place;
use App\Form\PlaceType;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/back/place')]
class PlaceController extends AbstractController
{
    /**
     * Display all places
     *
     * @param PlaceRepository $placeRepository
     * @return Response
     */
    #[Route('/', name: 'app_place_index', methods: ['GET'])]
    public function index(PlaceRepository $placeRepository): Response
    {
        return $this->render('back/place/index.html.twig', [
            'places' => $placeRepository->findAll(),
        ]);
    }

    /**
     * Create a place using a form
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/new', name: 'app_place_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $place = new Place();
        $form = $this->createForm(PlaceType::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($place);
            $entityManager->flush();

            $this->addFlash('success', 'Le lieu a bien été ajouté');

            return $this->redirectToRoute('app_place_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/place/new.html.twig', [
            'place' => $place,
            'form' => $form,
        ]);
    }

    /**
     * Display a single place by its id
     * 
     * @param Place $place
     * @return Response
     */
    #[Route('/{id}', name: 'app_place_show', methods: ['GET'])]
    public function show(Place $place): Response
    {
        return $this->render('back/place/show.html.twig', [
            'place' => $place,
        ]);
    }

    /**
     * Update a place by its id using a form
     * @param Request $request
     * @param Place $place
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_place_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Place $place, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PlaceType::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le lieu a bien été modifié');

            return $this->redirectToRoute('app_place_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/place/edit.html.twig', [
            'place' => $place,
            'form' => $form,
        ]);
    }

    /**
     * Delete a place by its id
     * @param Request $request
     * @param Place $place
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}', name: 'app_place_delete', methods: ['POST'])]
    public function delete(Request $request, Place $place, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$place->getId(), $request->request->get('_token'))) {
            $entityManager->remove($place);
            $entityManager->flush();

            $this->addFlash('success', 'Le lieu a bien été supprimé');
        }

        return $this->redirectToRoute('app_place_index', [], Response::HTTP_SEE_OTHER);
    }
}
