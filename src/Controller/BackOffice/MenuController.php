<?php

namespace App\Controller\BackOffice;

use App\Service\File;
use App\Service\ZamzarApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class MenuController extends AbstractController
{
    private $pathImgFolder;

    public function __construct(string  $publicFolderName)
    {
        $this->pathImgFolder = __DIR__ . '/../../../' . $publicFolderName . "/assets/pdf";
    }

     /**
     * @Route("admin/menu", name="app_menu_admin", methods={"GET", "POST"})
     */
    public function uploadPdfmenu(File $fileService, Request $request, ZamzarApi $zamzarApi)
    {
        $form = $this->createFormBuilder()
            ->add('pdf', FileType::class)
            
            ->getForm();

        $form->handleRequest($request);

        // verify if the form is empty 
        if ($form->isSubmitted() && $form->isValid() ) {
            $pdf = $form->get('pdf')->getData();
            
            if(null === $pdf){
                $this->addFlash('danger', 'Veuillez charger un fichier.');

                return $this->redirectToRoute('app_menu_admin'); 
            }
            $files = str_ends_with($pdf->getClientOriginalName(), '.pdf');
           
            // verify if the type of the file is pdf
            if ($files === true){   
                
                // if it'a a PDF, we moove him on our assets
                $pdf->move($this->pathImgFolder, 'cartes.pdf'); 
                
                
                // if the type is verify, we call the Zamzar API to convert the file to JPG and remove old menu 
                if(!$zamzarApi->jobRequest()) {

                    $this->addFlash('danger', 'Erreur lors de la conversion du PDF en image');

                    return $this->redirectToRoute('app_menu_admin');
                }

                $this->addFlash('success', 'La nouvelle carte a été ajoutée avec succès');  

            } else { 
                $this->addFlash('danger', 'Le fichier n\'est pas au format PDF');
            }   

            return $this->redirectToRoute('app_menu_admin');  
        }
        $allPictures = $fileService->getFiles('menu_page_*.jpg', true);

        return $this->render('menu/menu.html.twig', [
                'form' => $form->createView(),
                'allPictures' => $allPictures
        ]);
    }

    
}

