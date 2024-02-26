<?php

namespace App\Controller\Back;

use App\Entity\Picture;
use App\Form\PictureType;
use App\Service\FileUploader;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Route('/back/picture')]
class PictureController extends AbstractController
{

    private $fileUploader;

    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

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
     * @param TranslatorInterface $translator
     * @param FileUploader $fileUploader
     * @return Response
     */
    #[Route('/new', name: 'app_picture_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator, FileUploader $fileUploader): Response
    {
        $newPicture = new Picture();
        $form = $this->createForm(PictureType::class, $newPicture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $picture = $form->get('picture')->getData();

            if (!$picture) {

                return $this->addFlash('warning', 'warning.no_change');

            }
            $newFilename = $this->fileUploader->uploadImage($picture);
            $newPicture->setPicture($newFilename);
            $entityManager->persist($newPicture);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('confirmation.picture_uploaded'));


            return $this->redirectToRoute('app_picture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/picture/new.html.twig', [
            'picture' => $newPicture,
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
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_picture_edit', methods: ['GET', 'POST'])]

    public function edit(Request $request, Picture $picture, EntityManagerInterface $entityManager, TranslatorInterface $translator, ParameterBagInterface $params): Response

    {
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPicture = $form->get('picture')->getData();

            if (!$newPicture) {
                return $this->addFlash('warning', 'warning.no_change');
            }
            $deleteFileResult = $this->fileUploader->deletePictureFile($params->get('images_directory'), $picture->getPicture());

            if (!$deleteFileResult) {
                $this->addFlash('warning', 'warning.picture_deletion_failure');
            }
            $newFilename = $this->fileUploader->uploadImage($newPicture);
            $picture->setPicture($newFilename);
            $entityManager->persist($picture);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('confirmation.picture_updated'));


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
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/{id}', name: 'app_picture_delete', methods: ['POST'])]
    public function delete(Request $request, Picture $picture, EntityManagerInterface $entityManager, TranslatorInterface $translator, ParameterBagInterface $params): Response
    {
        $memoryId = $picture->getMemory()->getId();

        if ($this->isCsrfTokenValid('delete' . $picture->getId(), $request->request->get('_token'))) {
            $deleteFileResult = $this->fileUploader->deletePictureFile($params->get('images_directory'), $picture->getPicture());

            if (!$deleteFileResult) {

                $this->addFlash('warning', 'warning.picture_deletion_failure');
            }
            $entityManager->remove($picture);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('confirmation.picture_deleted'));

            return $this->redirectToRoute('app_memory_edit', ['id' => $memoryId]);
        }

        return $this->redirectToRoute('app_picture_index', [], Response::HTTP_SEE_OTHER);
    }
}
