<?php

namespace App\Controller\BackOffice;

use App\Entity\Picture;
use App\Form\PictureType;
use App\Repository\PageContentRepository;
use App\Repository\PictureRepository;
use App\Service\AllContents;
use App\Service\File;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use CURLFile;

class GalleryController extends AbstractController
{
    /**
     * @Route("/admin/gallery", name="app_gallery_index", methods={"GET"})
     */
    public function index(AllContents $allContents): Response
    {
        $currentPageAndLanguage = $allContents->getCurrentPageAndLanguageId("fr", "galerie");

        // Get all pictures for the current page
        $allPictures = $allContents->getCurrentPagePictures($currentPageAndLanguage
        ['pageId'], $currentPageAndLanguage['languageId']);

        return $this->render('gallery/index.html.twig', [
            'allPictures' => $allPictures,
        ]);
    }

    /**
     * @Route("/admin/gallery/add", name="new_picture_gallery")
     */
    public function addPicture(File $fileService, ManagerRegistry $managerRegistry, PageContentRepository $pageContentRepository, Request $request): Response
    {
        $galleryContentPage = $pageContentRepository->findOneBy(['page' => 4]);

        // creates a task object and initializes some data for this example
        $newpicture = new Picture();

        $form = $this->createForm(PictureType::class, $newpicture);
        $form->handleRequest($request);
        
        // check if the form is submit and valid
        if($form->isSubmitted() && $form->isValid()){

            $file = $form->get('path')->getData();

            if(!$fileService->isDownloadedFile($file)){
                $this->addFlash('danger', 'Image déjà existante');

                return $this->redirectToRoute('new_picture_gallery');
            }
            // we send the datas  in the bdd
            $newpicture->setPath($file->getClientOriginalName());
            $newpicture->addPageContent($galleryContentPage);
            $em = $managerRegistry->getManager();
            $em->persist($newpicture);
            $em->flush();

            $this->addFlash('success', 'Image ajoutée avec succès.');

            // redirect to the gallery home
            return $this->redirectToRoute('app_gallery_index');
        }

        return $this->renderForm('gallery/new.html.twig', [
            'form' => $form,
        ]);
    }

   

    /**
     * @Route("admin/gallery/{id}", name="app_gallery_delete", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function delete(File $fileService, Request $request, Picture $picture, PictureRepository $pictureRepository, PictureType $pictureType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$picture->getId(), $request->request->get('_token'))) {
            
            // use special function in PictureType to delete the file if exist and if he has the same name than the database
            $allFiles = $fileService->getFiles($picture->getPath()); 

            if(!$fileService->isDeleteFiles($allFiles)){
                $this->addFlash('danger', 'Erreur lors de la suppression de l\'image.');  
            }              
            // remove the file in the database
            $pictureRepository->remove($picture, true);

            $this->addFlash('success', 'Suppression de l\'image avec succès');
        }

        return $this->redirectToRoute('app_gallery_index', [], Response::HTTP_SEE_OTHER);
    }   
}
