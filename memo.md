<?php

namespace App\Controller\FrontOffice;

use App\Service\SendMail;
use App\Service\TokenCSRF;
use App\Service\AllContents;
use App\Repository\NewsRepository;
use App\Repository\PageContentRepository;
use App\Repository\PopUpRepository;
use App\Service\File;
use App\Service\ValidationUserValues;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    private $siteHost;

    public function __construct(string  $siteHost)
    {
        $this->siteHost = $siteHost;
    }
    /**
     * Get the content of the home page
     * 
     * @Route("/api/home", name="app_api_home", methods={"GET"})
     */
    public function getHomeContent(AllContents $allContents, NewsRepository $newsRepository, PopUpRepository $popUpRepository): JsonResponse
    {
        // Get all the news to be displayed on the home page.
        $homeNews = $newsRepository->findBy(['isHomeEvent' => true]);

        // Get the active pop-up if exist
        $popUp = $popUpRepository->findOneBy(['isActive' => true]);

        // Get all the content of the current page.
        $currentPageAndLanguage = $allContents->getCurrentPageAndLanguageId("fr" ,"accueil");

        $allCurrentPageContents = $allContents->getCurrentPageContents($currentPageAndLanguage['pageId'], $currentPageAndLanguage['languageId']);

        return $this->json([
                'page_content' => $allCurrentPageContents,
                'news' => $homeNews,
                'pop_up' => $popUp,
                'video' => $this->siteHost . "assets/videos/Tuileries_final.m4v",
            ],
            Response::HTTP_OK,
            [],
            // Groups used for news values
            ['groups' => ['get_news_item', 'get_pop_up_item']]
        );
    }

    /**
     * Get the content of the gourmand surprise page
     * 
     * @Route("/api/gourmand-surprise", name="app_api_gourmand_surprise", methods={"GET"})
     */
    public function getGourmandSurpriseContent(AllContents $allContents): JsonResponse
    {
        $currentPageAndLanguage = $allContents->getCurrentPageAndLanguageId("fr" ,"surprise gourmande");

        $allCurrentPageContents = $allContents->getCurrentPageContents($currentPageAndLanguage['pageId'], $currentPageAndLanguage['languageId']);

        return $this->json([
                'page_content' => $allCurrentPageContents,
            ],
            Response::HTTP_OK,
        );
    }

     /**
     * Get the content of the menu page
     * 
     * @Route("/api/menu", name="app_api_menu", methods={"GET"})
     */
    public function getMenuContent(AllContents $allContents, File $fileService, PageContentRepository $pageContentRepository): JsonResponse
    {
        $currentPageAndLanguage = $allContents->getCurrentPageAndLanguageId("fr" ,"menu");

        $currentPageContent = $pageContentRepository->findByPageId($currentPageAndLanguage['pageId']);
        // Get all pictures for the current page
        $allCurrentPagePictures = $fileService->getFiles('menu_page_*.jpg', true);

        return $this->json([
                'page_content' => $currentPageContent,
                'pictures'     => $allCurrentPagePictures 
            ],
            Response::HTTP_OK,
        );
    }

    /**
     * Get the content of the gallery page
     * 
     * @Route("/api/gallery", name="app_api_gallery", methods={"GET"})
     */
    public function getGalleryContent(AllContents $allContents, PageContentRepository $pageContentRepository): JsonResponse
    {
        $currentPageAndLanguage = $allContents->getCurrentPageAndLanguageId("fr" ,"galerie");

        $currentPageContent = $pageContentRepository->findByPageId($currentPageAndLanguage['pageId']);
        // Get all pictures for the current page
        $allCurrentPagePictures = $allContents->getCurrentPagePictures($currentPageAndLanguage['pageId'], $currentPageAndLanguage['languageId']);

        return $this->json([
                'page_content' => $currentPageContent,
                'pictures'     => $allCurrentPagePictures
                
            ],
            Response::HTTP_OK,
        );
    }

     /**
     * Get the content of the contact page
     * 
     * @Route("/api/contact", name="app_api_contact_get", methods={"GET"})
     */
    public function getContactContent(AllContents $allContents, TokenCSRF $token): JsonResponse
    {
        $currentPageAndLanguage = $allContents->getCurrentPageAndLanguageId("fr" ,"contact");

        $allCurrentPageContents = $allContents->getCurrentPageContents($currentPageAndLanguage['pageId'], $currentPageAndLanguage['languageId']);

        // Create a token
        $tokenCSRF = $token->createToken();

        return $this->json([
                'page_content' => $allCurrentPageContents,
                'token_CSRF' => $tokenCSRF,
            ],
            Response::HTTP_OK,  
        );
    }

     /**
     * Send the user mail
     * 
     * @Route("/api/contact", name="app_api_contact_post", methods={"POST"})
     */
    public function sendContactMail(Request $request, SendMail $sendMail, TokenCSRF $token, ValidationUserValues $validator): JsonResponse
    {
        // Get the CSRF token
        $tokenCSRFRequest = $request->headers->get('token-csrf');
        $isValidToken = $token->checkCSRFToken($tokenCSRFRequest);
        if(gettype($isValidToken) === "object"){

            return $isValidToken;
        }
        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true);
        if(!$validator->isKeyExist($data, ['email', 'firstname', 'lastname', 'subject', 'text'])) {

            return $this->json(["message" => "Une ou plusieurs information(s) manquante(s)"], Response::HTTP_BAD_REQUEST);
        }

        $userEmail = htmlspecialchars(filter_var($data['email'], FILTER_VALIDATE_EMAIL));
        $userFisrtname = htmlspecialchars($data['firstname']);
        $userLastname = htmlspecialchars($data['lastname']);
        $subject = htmlspecialchars($data['subject']);
        $text = htmlspecialchars($data['text']);

        if(
            empty($userEmail) ||
            empty($userFisrtname) ||
            empty($userLastname) ||
            empty($subject) ||
            empty($text)
        ) {
            return $this->json(["message" => "Une ou plusieurs informations sont manquantes"], Response::HTTP_BAD_REQUEST);
        }
        $sendMail->sendContactMail($userEmail, $userFisrtname, $userLastname, $subject, $text);
    
        return $this->json(["message" => "Message envoyé avec succès!"], Response::HTTP_OK);
    }

     /**
     * Get the content of the news page
     * 
     * @Route("/api/news", name="app_api_news", methods={"GET"})
     */
    public function getNewsContent(NewsRepository $newsRepository): JsonResponse
    {
        $allCurrentPageContents = $newsRepository->getAllNewsOrderByPublishedAt();

        return $this->json([
                'page_content' => $allCurrentPageContents,
            ],
            Response::HTTP_OK,
            [],
            ['groups' => 'get_news_item']  
        );
    }
}







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
