<?php

namespace App\Controller\Back;

use App\Entity\Location;
use App\Form\LocationType;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/back/location')]
class LocationController extends AbstractController
{
    /**
     * Display all locations
     *
     * @param LocationRepository $locationRepository
     * @return Response
     */
    #[Route('/', name: 'app_location_index', methods: ['GET'])]
    public function index(LocationRepository $locationRepository): Response
    {
        return $this->render('back/location/index.html.twig', [
            'locations' => $locationRepository->findAll(),
        ]);
    }

    /**
     * Create a location using a form
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/new', name: 'app_location_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($location);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('confirmation.location_added'));

            return $this->redirectToRoute('app_location_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/location/new.html.twig', [
            'location' => $location,
            'form' => $form,
        ]);
    }

    /**
     * Display a single location by its id
     * 
     * @param Location $location
     * @return Response
     */
    #[Route('/{id}', name: 'app_location_show', methods: ['GET'])]
    public function show(Location $location): Response
    {
        return $this->render('back/location/show.html.twig', [
            'location' => $location,
        ]);
    }

    /**
     * Update a location by its id using a form
     * @param Request $request
     * @param Location $location
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_location_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Location $location, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('confirmation.location_updated'));

            return $this->redirectToRoute('app_location_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/location/edit.html.twig', [
            'location' => $location,
            'form' => $form,
        ]);
    }

    /**
     * Delete a location by its id
     * @param Request $request
     * @param Location $location
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/{id}', name: 'app_location_delete', methods: ['POST'])]
    public function delete(Request $request, Location $location, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        if ($this->isCsrfTokenValid('delete'.$location->getId(), $request->request->get('_token'))) {
            
            if ($location->getMemories()) {
                $this->addFlash('danger', $translator->trans('warning.location_deleted'));
            return $this->redirect($request->headers->get('referer'));
            }
            $entityManager->remove($location);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('confirmation.location_deleted'));
        }

        return $this->redirectToRoute('app_location_index', [], Response::HTTP_SEE_OTHER);
    }
}