<?php

namespace App\Controller\Back;

use App\Entity\Picture;
use App\Form\PictureType;
use App\Repository\PictureRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/back/picture')]
class PictureController extends AbstractController
{
    /**
     * Display all pictures
     *
     * @param PictureRepository $pictureRepository
     * @return Response
     */
    #[Route('/', name: 'app_picture_index', methods: ['GET'])]
    public function index(PictureRepository $pictureRepository): Response
    {
        return $this->render('back/picture/index.html.twig', [
            'pictures' => $pictureRepository->findAllPicturesOrderedByMemoryId(),
        ]);
    }

    /**
     * Create a picture using a form
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/new', name: 'app_picture_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictures = $form->get('picture')->getData();

            if ($pictures) {
                $newPicture = $fileUploader->upload($pictures);
                $picture->setPicture($newPicture);
            }
            
            $entityManager->persist($picture);
            $entityManager->flush();


            return $this->redirectToRoute('app_picture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/picture/new.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }

    /**
     * Display a single picture by its id
     * 
     * @param Picture $picture
     * @return Response
     */
    #[Route('/{id}', name: 'app_picture_show', methods: ['GET'])]
    public function show(Picture $picture): Response
    {
        return $this->render('back/picture/show.html.twig', [
            'picture' => $picture,
        ]);
    }

    /**
     * Update a picture by its id using a form
     * @param Request $request
     * @param Picture $picture
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_picture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Picture $picture, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_picture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/picture/edit.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }

    /**
     * Delete a picture by its id
     * @param Request $request
     * @param Picture $picture
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}', name: 'app_picture_delete', methods: ['POST'])]
    public function delete(Request $request, Picture $picture, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$picture->getId(), $request->request->get('_token'))) {
            $entityManager->remove($picture);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_picture_index', [], Response::HTTP_SEE_OTHER);
    }
}
