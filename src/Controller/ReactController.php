<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReactController extends AbstractController
{
    private $pathImgFolder;

    public function __construct(string  $publicFolderName)
    {
        $this->pathImgFolder = __DIR__ . '/../../' . $publicFolderName;
    }

    /**
     * @Route("/{reactRouting}", name="app_react", priority="-1", defaults={"reactRouting": null}, requirements={"reactRouting"=".+"})
     */
    public function index()
    {
        // Get all js and css files generate by 'yarn build' 
        $fullPathJsFiles = glob($this->pathImgFolder . '/js/*.js');
        $fullPathCssFiles = glob($this->pathImgFolder . '/css/*.css');

        // Just take the name of the files
        $allJsFiles = [];
        $allCssFiles = [];
        foreach($fullPathJsFiles as $file){
            $allJsFiles[] = basename($file);
        }
        foreach($fullPathCssFiles as $file){
            $allCssFiles[] = basename($file);
        }

        return $this->render('react/index.html.twig', [
            'jsFiles' => $allJsFiles,
            'cssFiles' => $allCssFiles
        ]);
    }
}
