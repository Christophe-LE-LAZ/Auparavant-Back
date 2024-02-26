<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LanguageController extends AbstractController
{
    /**
     * Language switching management
     * @param
     * @return Response
     */
    #[Route('/{_locale}/switch-language', name: 'switch_language')]
    public function switchLanguage(Request $request, $_locale): Response
    {
        $request->getSession()->set('_locale', $_locale);
        return $this->redirect($request->headers->get('referer'));
    }
}