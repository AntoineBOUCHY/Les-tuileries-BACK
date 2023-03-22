<?php

namespace App\Controller\BackOffice;

use App\Entity\PopUp;
use App\Form\PopUpType;
use App\Repository\PopUpRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/popup")
 */
class PopUpController extends AbstractController
{
    /**
     * @Route("/", name="app_pop_up_index", methods={"GET", "POST"})
     */
    public function index(Request $request, PopUpRepository $popUpRepository): Response
    {
        $popUp = $popUpRepository->find(1);
        $form = $this->createForm(PopUpType::class, $popUp);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $popUpRepository->add($popUp, true);
            $this->addFlash('success', 'La Pop up a été mise à jour avec succés');
            return $this->redirectToRoute('app_pop_up_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('pop_up/index.html.twig', [
            'pop_up' => $popUp,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_pop_up_status", methods={"GET"})
     */
    public function toogleStatus(PopUp $popUp, PopUpRepository $popUpRepository): Response
    {
        $popUp->setIsActive(!$popUp->isIsActive());
        $popUpRepository->add($popUp, true);

        return $this->redirectToRoute('app_pop_up_index', [], Response::HTTP_SEE_OTHER);
    }
}
