<?php

namespace App\Controller\BackOffice;

use App\Entity\Card;
use App\Entity\User;
use App\Form\CardType;
use DateTimeImmutable;
use App\Service\SurpriseCard;
use App\Repository\CardRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class CardController extends AbstractController
{
    public function error404Action(): Response
    {
        return $this->render('TwigBundle/Exception/error404.html.twig', []);
    }
    /**
     * @Route("/admin/cards", name="app_card_index", methods={"GET"})
     */
    public function index(CardRepository $cardRepository): Response
    {
        return $this->render('card/index.html.twig', [
            'cards' => $cardRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/card/new", name="app_card_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CardRepository $cardRepository): Response
    {
        $card = new Card();
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Create a random reference
        $cardReference = rand(1, 999999);
        // Condition of while loop
        $isUnique = false;
        while(!$isUnique){
            // Check if a card is find with this reference
            $cardReferenceExist = $cardRepository->findOneBy(['reference' => $cardReference]);
            if(null === $cardReferenceExist){
                // The reference does not exist in the DB
                $isUnique = true;
                break;
            }
            // Generate a new reference
            $cardReference = rand(1, 999999);
        }
            $card->setReference($cardReference);
            $cardRepository->add($card, true);

            $this->addFlash('success', 'Card créée avec succès.');

            return $this->redirectToRoute('app_card_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('card/new.html.twig', [
            'card' => $card,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/infos", name="app_infos", methods={"GET", "POST"})
     */
    public function newcardfront (Request $request, CardRepository $cardRepository): Response
    {
        $card = new Card();
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {       
       
            // Create a random reference
        $cardReference = rand(1, 999999);
        // Condition of while loop
        $isUnique = false;
        while(!$isUnique){
            // Check if a card is find with this reference
            $cardReferenceExist = $cardRepository->findOneBy(['reference' => $cardReference]);
            if(null === $cardReferenceExist){
                // The reference does not exist in the DB
                $isUnique = true;
                break;
            }
            // Generate a new reference
            $cardReference = rand(1, 999999);
        }
            $card->setReference($cardReference);
            $cardRepository->add($card, true);

            $this->get('session')->set('card_id', $card->getId());





            return $this->redirectToRoute('app_stripe', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('front/infos.html.twig', [
            'card' => $card,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/create-checkout-session", name="app_stripe")
     */
    public function paiement(Request $request,CardRepository $cardRepository): Response
    {

        $cardId = $this->get('session')->get('card_id');
        if (empty($cardId)) {
            return $this->redirectToRoute('app_cadeau');
        }
        $card = $cardRepository->find($cardId);
        
    
        $stripe = new \Stripe\StripeClient('sk_test_51MWFV9HkQdmkPkOMQyZn0u7sVgCaah4JT8PZ6IZXVMLcm9qe0pbIIhageWXxMOgvuxyVSO0aSjkmXhIquMPtzous00s5OmZOnk');
        
$checkout_session = $stripe->checkout->sessions->create([
    'customer_email' => $card->getEmail(),
    // $card->getEmail(),
    'line_items' => [[
        'price_data' => [
        'currency' => 'eur',
        'product_data' => [
            'name' => 'Carte cadeau d\'une valeur de :',
        ],
        'unit_amount' => $card->getAmount()."00",
        // $card->getAmount(),
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => 'http://localhost:8000/confirmation',
    'cancel_url' => 'http://localhost:8000/annulation',
    ]);
    $this->get('session')->set('card_id', $card->getId());
    

    
        return $this->redirect($checkout_session->url);
        
    }
    
     /**
     * @Route("/confirmation", name="app_success")
     */
    public function success(CardRepository $cardRepository, SurpriseCard $surpriseCard): Response
    {
        $cardId = $this->get('session')->get('card_id');
        dd($cardId);
        
        if (empty($cardId)) {
            return $this->redirectToRoute('app_cadeau');
        }
        
        $card = $cardRepository->find($cardId);
        // dd($card->getEmail());
        
        $pathPDF = $surpriseCard->createCard($card->getReference(), $card->getGifter(), $card->getReceiver(), $card->getAmount(), $card->getLimitedDate()->format('d/m/Y'));

        

    return $this->renderForm('front/success.html.twig', [
        'card' => $card,
        'pdf'=> $pathPDF,        
    ]);
    }

    /**
     * @Route("/annulation", name="app_cancel")
     */
    public function canceled(CardRepository $cardRepository): Response
    {
        return $this->render('front/canceled.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }


    /**
     * @Route("/admin/card/{id}", name="app_card_show", methods={"GET"})
     */
    public function show(Card $card): Response
    {
        
        return $this->render('card/show.html.twig', [
            'card' => $card,
            
        ]);
    }
    
    /**
     * @Route("admin/card/{id}/usedat", name="app_card_used", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function addUsedAt (Card $card, ManagerRegistry $managerRegistry) :response
    {
        $id = $card->getId();         
        $used = $card->setUsedAt(New DateTimeImmutable());       
        $em = $managerRegistry->getManager();
        $em->persist($used);            
        $em->flush();
        
        $this->addFlash('success', 'Carte désactivée avec succès');
        

        return $this->redirectToRoute('app_card_show', [
            'id' => $id,
            'used' => $used,
            
        ], Response::HTTP_SEE_OTHER);     
    }

    /**
     * @Route("admin/card/search", name="app_card_search", methods={"POST"})
     */
    public function searchCard(CardRepository $cardRepository, Request $request)
    {
        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent);
        $cards = $cardRepository->search($data);

        return $this->json([
                'cards' => $cards
            ],
            Response::HTTP_OK
        );
    }
}
    