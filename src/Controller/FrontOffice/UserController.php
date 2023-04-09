<?php

namespace App\Controller\FrontOffice;

use App\Entity\User;
use App\Service\TokenCSRF;
use App\Repository\UserRepository;
use App\Service\ValidationUserValues;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{
    private $tokenService;

    public function __construct(TokenCSRF $token)
    {
        $this->tokenService = $token;
        // profiler : e1e1c1 -> 7075f8 -> 5dbfc1
    }

    // /**
    //  * Send the CSRF token for sign in user form
    //  * 
    //  * @Route("/api/sign-in", name="app_user_sign_in_get", methods={"GET"})
    //  */
    // public function signInCSRF(): JsonResponse
    // {
    //     // Create a token
    //     $tokenCSRF = $this->tokenService->createToken();

    //     return $this->json(['token_CSRF' => $tokenCSRF], Response::HTTP_OK);
    // }

    /**
     * Create a new user
     * 
     * @Route("/sign-in", name="app_user_sign_in_post", methods={"POST"})
     */
    public function signIn(ManagerRegistry $managerRegistry ,Request $request, SerializerInterface $serializerInterface, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository, ValidationUserValues $validator ): JsonResponse
    {
        // Get the content of the request
        $jsonContent = $request->getContent();

        // For verify the email and password confirmation
        $confirmationData = json_decode($jsonContent, true);

        // Get CSRF token in the header of the request
        $tokenCSRFRequest = $request->headers->get('token-csrf');
        $this->tokenService->checkCSRFToken($tokenCSRFRequest);

        // Create a new user with the value of the request
        /**@var User */
        $newUser = $serializerInterface->deserialize($jsonContent, User::class, 'json');
        
        // Check if each value respects the validation constraints
        if($validator->isValidEntity($newUser)) {
            
            return $this->json(['message' => 'Une ou plusieurs valeur(s) incorrecte(s)'], Response::HTTP_BAD_REQUEST);
        }

        // // Check if the email exist in the DB
        // if ($userRepository->findOneBy(['email' =>$newUser->getEmail()])) {

        //     return $this->json(['message' => 'Adresse mail déjà utilisée!'], Response::HTTP_BAD_REQUEST);
        // }

        // Check if the email and the password are correctly entered
        if(
            $newUser->getEmail() !== $confirmationData['email_confirmation'] ||
            $newUser->getPassword() !== $confirmationData['password_confirmation']
        ) {

            return $this->json(['message' => 'La double saisie du mot de pass ou de l\'email est incorrect'], Response::HTTP_BAD_REQUEST);
        }


        $em = $managerRegistry->getManager();
        // Hash the password
        $hashedPassword = $passwordHasher->hashPassword(
            $newUser,
            $newUser->getPassword()
        );
        // Save the hashed password
        $newUser->setPassword($hashedPassword);
        $em->persist($newUser);
        $em->flush();

        return $this->json([
                'message' => "Utilisateur créé",
                $newUser
            ],
            Response::HTTP_CREATED,
            [],
            ['groups' => 'users_get_item']
        );
    }

    /**
     * Create a new user
     * 
     * @Route("/api/create-admin", name="app_user_create_admin", methods={"POST"})
     */
    public function fakeAdmin(UserPasswordHasherInterface $passwordHasher, ManagerRegistry $managerRegistry): JsonResponse
    {
        $admin = new User();
        $admin->setEmail("admin@admin.com")
              ->setFirstname("admin")
              ->setLastname("admin")
              ->setPhone("02000000")
              ->setPassword("admin")
              ->setRoles(["ROLE_ADMIN"]);

        $em = $managerRegistry->getManager();
        // Hash the password
        $hashedPassword = $passwordHasher->hashPassword(
            $admin,
            $admin->getPassword()
        );
        // Save the hashed password
        $admin->setPassword($hashedPassword);
        $em->persist($admin);
        $em->flush();

        return $this->json([
            'message' => "Utilisateur créé",
            $admin
        ],
        Response::HTTP_CREATED,
        [],
        ['groups' => 'users_get_item']
    );
    }

    /**
     * Get the content of the client space page
     * 
     * @Route("/api/client-space", name="app_user_client_space", methods={"GET"})
     */
    public function clientSpace(TokenStorageInterface $tokenStorageInterface): JsonResponse
    {
        // Get the user connected
        $user = $tokenStorageInterface->getToken()->getUser();

        // Create a token
        $tokenCSRF = $this->tokenService->createToken();

        return $this->json([
                'user_informations' => $user,
                'token_CSRF' => $tokenCSRF
            ],
            Response::HTTP_OK,
            [],
            ['groups' => 'users_get_item']
        );
    }

    /**
     * Update the user informations
     * 
     * @Route("/api/client-space", name="app_user_client_space_edit", methods={"PUT"})
     */
    public function editClientSpace(Request $request, ManagerRegistry $managerRegistry, TokenStorageInterface $tokenStorageInterface, ValidationUserValues $validator): JsonResponse
    {
        // Check CSRF token
        $tokenCSRFRequest = $request->headers->get('token-csrf');
        $this->tokenService->checkCSRFToken($tokenCSRFRequest);

        // Get the content  of the request
        $jsonContent = $request->getContent();
        // Convert content to an associative array
        $data = json_decode($jsonContent, true);
        // Check if all the necessary keys exist
        if(!$validator->isKeyExist($data, ['email', 'firstname', 'lastname', 'phone'])) {
            
            return $this->json(['message' => 'Une ou plusieurs information(s) manquante(s)'], Response::HTTP_BAD_REQUEST);
        }

        // Save each value
        $userEmail = htmlspecialchars(filter_var($data['email'], FILTER_VALIDATE_EMAIL));
        $userFisrtname = htmlspecialchars($data['firstname']);
        $userLastname = htmlspecialchars($data['lastname']);
        $userPhone = htmlspecialchars($data['phone']);

        // Check if all values are not empty
        if (
            empty($userEmail) ||
            empty($userFisrtname) ||
            empty($userLastname) ||
            empty($userPhone)
        ) {
            return $this->json(['message' => 'Une ou plusieurs information(s) manquante(s)'], Response::HTTP_BAD_REQUEST);
        }
        // Get the user connected
        /**@var User */
        $user = $tokenStorageInterface->getToken()->getUser();

        // Check if the email can be accepted
        if ($validator->isEmailExist($userEmail, $user->getEmail())) {

            return $this->json(['message' => 'Adresse mail déjà utilisée!'], Response::HTTP_BAD_REQUEST);
        }

        $user->setEmail($userEmail)
             ->setFirstName($userFisrtname)
             ->setLastname($userLastname)
             ->setPhone($userPhone);

        $em = $managerRegistry->getManager();
        $em->persist($user);
        $em->flush();

        // Return the informations updated
        return $this->json([
                'user_informations' => $user
            ],
            Response::HTTP_OK,
            [],
            ['groups' => 'users_get_item']
        );
    }

    /**
     * Delete the user
     * 
     * @Route("/api/client-space/delete", name="app_user_client_space_delete", methods={"DELETE"})
     */
    public function deleteUser(Request $request, TokenStorageInterface $tokenStorageInterface, UserRepository $userRepository): JsonResponse
    {
        $requestTokenCSRF = $request->headers->get('token-csrf');
        $this->tokenService->checkCSRFToken($requestTokenCSRF);
        // Get the user connected
        /**@var User */
        $user = $tokenStorageInterface->getToken()->getUser();

        // Delete the user
        $userRepository->remove($user, true);

        return $this->json(['user_informations' => $user], Response::HTTP_NO_CONTENT);
    }
}
