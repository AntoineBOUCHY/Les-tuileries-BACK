<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    #[Route('/', name: 'app_front')]
    public function index(): Response
    {
        return $this->render('front/index.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }

    #[Route('/menus', name: 'app_menus')]
public function menus(): Response
    {
        return $this->render('front/menus.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }
    #[Route('/galerie', name: 'app_galerie')]
public function galerie(): Response
    {
        return $this->render('front/galerie.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }
    #[Route('/evenements', name: 'app_event')]
public function event(): Response
    {
        return $this->render('front/event.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }
}

