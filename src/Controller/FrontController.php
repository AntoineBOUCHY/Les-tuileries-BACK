<?php

namespace App\Controller;

use App\Service\AllContents;
use App\Repository\NewsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\SurpriseCard;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="app_front")
     */    
    public function index(): Response
    {
        return $this->render('front/index.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }

    /**
     * @Route("/menus", name="app_menus")
     */
    public function menus(): Response
    {
        return $this->render('front/menus.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }

    /**
     * @Route("/galerie", name="app_galerie")
     */
    public function galerie(AllContents $allContents): Response
    {
        $currentPageAndLanguage = $allContents->getCurrentPageAndLanguageId("fr", "galerie");

        // Get all pictures for the current page
        $allPictures = $allContents->getCurrentPagePictures($currentPageAndLanguage
        ['pageId'], $currentPageAndLanguage['languageId']);

        return $this->render('front/galerie.html.twig', [
            'allPictures' => $allPictures,
        ]);
    }

    /**
     * @Route("/evenements", name="app_event")
     */
    public function event(NewsRepository $newsRepository): Response
    {
        return $this->render('front/event.html.twig', [
            'allNews' => $newsRepository->getAllNewsOrderByPublishedAt()
        ]);
    }

    /**
     * @Route("/carte_cadeau", name="app_cadeau")
     */
    public function cadeau(): Response
    {
        return $this->render('front/cadeau.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }

      /**
     * @Route("/25467437853925", name="app_esteregg")
     */
    public function esteregg(): Response
    {
        return $this->render('front/egg.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }
   
}