<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class LanguageController extends AbstractController
{
    public function switchLanguage(Request $request, string $locale): Response
    {
        dump("Switching language to: " . $locale);
        $request->getSession()->set('_locale', $locale);

        // Redirect back to the referring page
        return $this->redirect($request->headers->get('referer'));
    }
}
