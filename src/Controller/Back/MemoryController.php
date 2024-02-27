<?php

namespace App\Controller\Back;

use App\Entity\Memory;
use App\Entity\Picture;
use App\Form\MemoryType;
use App\Service\FileUploader;
use App\Repository\MemoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


#[Route('/back/memory')]
class MemoryController extends AbstractController
{

    private $fileUploader;

    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }


    /**
     * Display all memories
     *
     * @param MemoryRepository $memoryRepository
     * @return Response
     */
    #[Route('/', name: 'app_memory_index', methods: ['GET'])]
    public function index(MemoryRepository $memoryRepository): Response
    {
        return $this->render('back/memory/index.html.twig', [
            'memories' => $memoryRepository->findAll(),
        ]);
    }

    /**
     * Create a memory using a form
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/new', name: 'app_memory_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        $memory = new Memory();
        $form = $this->createForm(MemoryType::class, $memory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $picture = $form->get('main_picture')->getData();

            if ($picture === null) {
                $this->addFlash('warning', 'warning.memory_picture_mandatory');
            } else {
                $newFilename = $this->fileUploader->uploadImage($picture);
                $memory->setMainPicture($newFilename);
            }

            $additionalPictures = $form->get('additionalPictures')->getData();

            foreach ($additionalPictures as $additionalPicture) {

                $newFilename = $this->fileUploader->uploadImage($additionalPicture);
                $newPicture = new Picture();
                $newPicture->setPicture($newFilename);
                $memory->addPicture($newPicture);
                $entityManager->persist($newPicture);
            }

            $entityManager->persist($memory);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('confirmation.memory_created'));

            return $this->redirectToRoute('app_memory_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/memory/new.html.twig', [
            'memory' => $memory,
            'form' => $form,
        ]);
    }

    /**
     * Display a single memory by its id
     * 
     * @param Memory $memory
     * @return Response
     */
    #[Route('/{id}', name: 'app_memory_show', methods: ['GET'])]
    public function show(Memory $memory): Response
    {
        return $this->render('back/memory/show.html.twig', [
            'memory' => $memory,
        ]);
    }

    /**
     * Update a memory by its id using a form
     * @param Request $request
     * @param Memory $memory
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_memory_edit', methods: ['GET', 'POST'])]

    public function edit(Request $request, Memory $memory, EntityManagerInterface $entityManager, ParameterBagInterface $params, TranslatorInterface $translator): Response

    {
        $form = $this->createForm(MemoryType::class, $memory, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $picture = $form->get('main_picture')->getData();

            if ($picture === null) {
                // if the user forgets to insert a photo, I keep the current photo in memory
                if ($memory->getMainPicture()) {

                    $entityManager->flush();
                } else {

                    $this->addFlash('warning', 'warning.no_change');
                }
                // otherwise I recover the current photo, delete it from the asset pictures folder and the database and save the new one, changing its name.
            } else {

                if ($memory->getMainPicture()) {

                    $deleteFileResult = $this->fileUploader->deletePictureFile($params->get('images_directory'), $memory->getMainPicture());
                    if (!$deleteFileResult) {
                        return $this->addFlash('warning', 'warning.memory_picture_update_failure', 500);
                    }
                }

                $newFilename = $this->fileUploader->uploadImage($picture);
                $memory->setMainPicture($newFilename);
            }

                $additionalPictures = $form->get('additionalPictures')->getData();

    
                foreach ($additionalPictures as $additionalPicture) {

                    $newFilename = $this->fileUploader->uploadImage($additionalPicture);
                    $newPicture = new Picture();
                    $newPicture->setPicture($newFilename);
                    $memory->addPicture($newPicture);
                    $entityManager->persist($newPicture);
                }

                $entityManager->flush();

                $this->addFlash('success', $translator->trans('confirmation.memory_updated'));
                return $this->redirectToRoute('app_memory_index', [], Response::HTTP_SEE_OTHER);
            }

        return $this->render('back/memory/edit.html.twig', [
            'memory' => $memory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete a memory by its id
     * @param Request $request
     * @param Memory $memory
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/{id}', name: 'app_memory_delete', methods: ['POST'])]
    public function delete(Request $request, Memory $memory, EntityManagerInterface $entityManager, ParameterBagInterface $params, TranslatorInterface $translator): Response
    {
        if ($this->isCsrfTokenValid('delete' . $memory->getId(), $request->request->get('_token'))) {

            // Delete all associated pictures in the 'picture' table
            foreach ($memory->getPicture() as $picture) {

                $deletePictureResult = $this->fileUploader->deletePictureFile($params->get('images_directory'), $picture->getPicture());

                if (!$deletePictureResult) {
                    $this->addFlash('warning', 'warning.memory_picture_deletion_failure');
                }
            }
            // delete main picture
            $deleteMainPictureResult = $this->fileUploader->deletePictureFile($params->get('images_directory'), $memory->getMainPicture());

            $entityManager->remove($memory);
            $entityManager->flush();

        // Retrieve the locality associated with the memory
        $location = $memory->getLocation();

       // Delete the locality if there are no other associated memories
        if ($location && $location->getMemories()->isEmpty()) {
        $entityManager->remove($location);
        $entityManager->flush();

        }

        // Retrieve the place associated with the location
        $place = $memory->getPlace();
        // Delete location if there are no other associated localities
        if ($place && $place->getMemories()->isEmpty()) {
            $entityManager->remove($place);
            $entityManager->flush();
        }

        $this->addFlash('success', $translator->trans('confirmation.memory_deleted'));

        return $this->redirectToRoute('app_memory_index', [], Response::HTTP_SEE_OTHER);
    }
}
}
