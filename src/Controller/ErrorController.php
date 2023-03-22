<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    /**
     * Return 404 error message
     * 
     * @Route("/api/error/404", name="app_error_404")
     */
    public function error404(): JsonResponse
    {
        return $this->json([
            'message' => 'Page non trouvée!'
        ]);
    }
}
