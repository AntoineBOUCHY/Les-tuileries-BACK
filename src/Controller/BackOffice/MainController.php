<?php

namespace App\Controller\BackOffice;

use App\Repository\PopUpRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @Route("/admin/home", name="app_home_admin")
     */
    public function index(PopUpRepository $popUpRepository): Response
    {
        $popUp = $popUpRepository->find(1);
        return $this->render('main/home.html.twig', [
            'controller_name' => 'MainController',
            'pop_up' => $popUp,
        ]);
    }
}
