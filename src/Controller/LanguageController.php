<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;

class LanguageController extends AbstractController
{


    public function switchLanguage(LocaleSwitcher $localeSwitcher,Request $request, string $locale): Response
    {
        $request->getSession()->set('locale', $locale);
        //$locale = $request->getLocale();
        $localeSwitcher->setLocale($locale);
       
        // Redirect back to the referring page
        return $this->redirect($request->headers->get('referer'));
    }

    
    

    
}

        
