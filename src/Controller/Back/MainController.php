<?php

namespace App\Controller\Back;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * Display backoffice home page
     *
     * @return Response
     */
    #[Route('/back', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('back/main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/translation/{locale}', name: 'app_translation')]
    public function translation($locale, Request $request): Response
    {
        
        $request->getSession()->set('_locale', $locale);
      
        return $this->redirect($request->headers->get('referer'));
    }
}
