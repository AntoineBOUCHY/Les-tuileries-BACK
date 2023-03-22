<?php

namespace App\Controller\BackOffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * @Route("/admin/login", name="app_login_admin")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Les deux lignes suivantes servent en cas d'échec à la connexion
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig',[
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }
}
