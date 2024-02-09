<?php

namespace App\Controller\Back;

use App\Entity\Memory;
use App\Form\MemoryType;
use App\Repository\MemoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/back/memory')]
class MemoryController extends AbstractController
{
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
     * @return Response
     */
    #[Route('/new', name: 'app_memory_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $memory = new Memory();
        $form = $this->createForm(MemoryType::class, $memory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($memory);
            $entityManager->flush();

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
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_memory_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Memory $memory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MemoryType::class, $memory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_memory_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/memory/edit.html.twig', [
            'memory' => $memory,
            'form' => $form,
        ]);
    }

    /**
     * Delete a memory by its id
     * @param Request $request
     * @param Memory $memory
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}', name: 'app_memory_delete', methods: ['POST'])]
    public function delete(Request $request, Memory $memory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$memory->getId(), $request->request->get('_token'))) {
            $entityManager->remove($memory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_memory_index', [], Response::HTTP_SEE_OTHER);
    }
}
