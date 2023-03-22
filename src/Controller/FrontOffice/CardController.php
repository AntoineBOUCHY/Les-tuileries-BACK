<?php

namespace App\Controller\FrontOffice;

use App\Entity\Card;
use App\Entity\User;
use App\Repository\CardRepository;
use App\Service\Paypal;
use App\Service\SendMail;
use App\Service\TokenCSRF;
use App\Service\SurpriseCard;
use App\Service\ValidationUserValues;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CardController extends AbstractController
{
    /**
     * Buy a gourmand surprise
     * 
     * @Route("/api/payment", name="app_capture", methods={"POST"})
     */
    public function payment(CardRepository $cardRepository, Paypal $paypal, Request $request, SendMail $mail, SurpriseCard $surpriseCard, SessionInterface $sessionInterface, TokenStorageInterface $tokenStorageInterface)
    {
        // Get the request content 
        $jsonContent = $request->getContent();
        // Convert in associative array 
        $data = json_decode($jsonContent, true);

        // Check if the authorization is completed
        if(array_key_exists('status', $data) && $data['status'] !== 'COMPLETED'){

            return $this->json([
                'message' => 'le paiement n\'a pas été autorisé',
                Response::HTTP_BAD_REQUEST
            ]);
        }

        // Get the currency code
        $amountCurrency = $data['purchase_units'][0]['amount']['currency_code'];

        // Check if the currency is "EUR"
        if("EUR" !== $amountCurrency){

            return $this->json([
                    'message' => 'La devise doit être en euros',
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Get the id of the authorization
        $authorizationId = $data['purchase_units'][0]['payments']['authorizations'][0]['id'];
        // Capture the payment
        $capture = $paypal->getCapture($authorizationId);

        // Check if the capture was successfull
        if('array' !== gettype($capture)){

            return $this->json([
                    'message' => 'Une erreur est survenue, paiement annulé'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
        // Get the user
        /**@var User */
        $user = $tokenStorageInterface->getToken()->getUser();
        // Get the value of the amount
        $amountValue = $capture['amount']['value'];

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

        $newCard = (new Card())
                ->setAmount($amountValue)
                ->setGifter($sessionInterface->get('informations')['gifter'])
                ->setReceiver($sessionInterface->get('informations')['receiver'])
                ->setReference($cardReference)
                ->setUser($user);
                ;
        $cardRepository->add($newCard, true);
            
        // Create the PDF of the surprise card
        $pathPDF = $surpriseCard->createCard($newCard->getReference(), $newCard->getGifter(), $newCard->getReceiver(), $amountValue, $newCard->getLimitedDate()->format('d/m/Y'));
        // Send the surprise card mail
        $isSendMail = $mail->sendCardMail($user->getEmail(), $user->getFirstname(), $user->getLastname(), $amountValue, $pathPDF);
        // Check if an error occured while sending the mail
        if(false === $isSendMail){

            return $this->json([
                        'message' => 'Paiement réussi mais erreur lors de l\'envoie du mail. Veuillez réessayer à partir de votre espace client ou appeler le restaurant.'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
        // Delete the PDF file on the server
        unlink($pathPDF);

        return $this->json([
                'message' => 'Paiement accepté, carte créée'
            ],
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/api/buy-card", name="app_buy_card", methods={"POST"})
     */
    public function buyCard(Request $request, SessionInterface $sessionInterface): Response
    {
        // Get request content
        $jsonContent = $request->getContent();
        // Convert to associative array
        $allData = json_decode($jsonContent, true);

        $gifter = htmlspecialchars($allData['gifter']);
        $receiver = htmlspecialchars($allData['receiver']);
        $amount = htmlspecialchars($allData['amount']);

        // Save all informations in a session
        $informations = [
            'gifter' => $gifter,
            'receiver' => $receiver,
            'amount' => $amount
        ];
        $sessionInterface->set('informations', $informations);

        return $this->json([
            'message' => 'la redirection vers le moyen de paiement peut être effectuée'
        ],
        Response::HTTP_OK
    );
    }

    /**
     * @Route("/api/purchase-form", name="app_purchase_form", methods={"GET"})
     */
    public function purchaseForm(Paypal $paypal, SessionInterface $sessionInterface): Response
    {
        return $this->render('paypal/paypal.html.twig', [
            'paypalId' => $paypal->getClientId(),
            'amountValue' => $sessionInterface->get('informations')['amount']
        ]);
    }
}
