<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class LanguageController extends AbstractController
{
    public function switchLanguage(Request $request, string $locale): Response
    {
        $request->getSession()->set('_locale', $locale);

        $referer = $request->headers->get('referer');

        // Redirect back to the referring page
        return $this->redirect($referer);
    }
}
