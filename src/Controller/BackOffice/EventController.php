<?php

namespace App\Controller\BackOffice;

use App\Entity\News;
use App\Entity\Picture;
use App\Form\NewsType;
use App\Repository\NewsRepository;
use App\Repository\PictureRepository;
use App\Service\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @Route("/event")
 */
class EventController extends AbstractController
{
    /**
     * @Route("/admin/news", name="app_event_index", methods={"GET"})
     */
    public function index(NewsRepository $newsRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'allNews' => $newsRepository->getAllNewsOrderByPublishedAt()]);

    }

    /**
     * @Route("/new", name="app_event_new", methods={"GET", "POST"})
     * @Route("/{id}/edit", name="app_event_edit", methods={"GET", "POST"})
     */
    public function new(File $fileService, Request $request, ?News $news, NewsRepository $newsRepository, ManagerRegistry $managerRegistry): Response
    {
        // To personalize the flash message
        $creation = false;
        if(null === $news){
            $news = new News();
            $creation = true;
        }
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $managerRegistry->getManager();
            // Check if this news is displayed on the home page
            $isEvent=$news->isIsHomeEvent();
            if ($isEvent) {
                // Check if another news is displayed on the home page
                $otherNews = $newsRepository->findBy(['isHomeEvent' => true]);
                // Remove the other news of the home page
                foreach ($otherNews as $otherNew) {
                    if ($otherNew->getId() !== $news->getId()) {
                        $otherNew->setIsHomeEvent(false);
                    }
                }
            }
            // If the admin add a image with the news
            if (null !== $news->getPictures()['path']) {
                $file = $form->get('pictures')->get('path')->getData();
                
                if(!$fileService->isDownloadedFile($file)){
                    $this->addFlash('danger', 'Image déjà existante');

                    return $this->redirectToRoute('app_event_new');
                }

                $newPicture = (new Picture())
                            ->setPath($file->getClientOriginalName())
                            ->setAlt($form->get('pictures')->get('alt')->getData())
                            ->setNews($news);

                // Empty the collection
                $news->emptyPicture();
                // Add the new picture to the collection
                $news->addPicture($newPicture);
                $em->persist($newPicture);

            } else {
                // empty the collection of pictures
                $news->emptyPicture();
            }
                
            
            $em->persist($news);
            $em->flush(); 
            
            if($creation){
                $this->addFlash('success', 'Evenement créé avec succès.');
            } else {
                $this->addFlash('success', 'Evenement modifié avec succès.');
            }

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/new.html.twig', [
            'news' => $news,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/picture/{id}", name="app_event_picture_delete", methods={"POST"})
     */
    public function deletePicture(File $fileService, Request $request, Picture $picture, PictureRepository $pictureRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$picture->getId(), $request->request->get('_token'))) {
            // use special function in PictureType to delete the file if exist and if he has the same name than the database
            $allFiles = $fileService->getFiles($picture->getPath()); 

            if(!$fileService->isDeleteFiles($allFiles)){

                $this->addFlash('danger', 'Erreur lors de la suppression de l\'image.');
            }        
            
            $this->addFlash('success', 'Image supprimée avec succès.');
            // remove the file in the database
            $pictureRepository->remove($picture, true);
        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("/{id}", name="app_event_delete", methods={"POST"})
     */
    public function delete(Request $request, News $news, NewsRepository $newsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$news->getId(), $request->request->get('_token'))) {
            $newsRepository->remove($news, true);

            $this->addFlash('success', 'Evenement supprimé avec succès.');
        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }
}
