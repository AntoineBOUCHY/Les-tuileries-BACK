<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class TokenCSRF
{
    private $sessionInterface;

    public function __construct(SessionInterface $sessionInterface)
    {
        $this->sessionInterface = $sessionInterface;
    }
    /**
     * Create a token to counter CSRF attack
     *
     * @return string
     */
    public function createToken(): string
    {
        $tokenCSRF = bin2hex(openssl_random_pseudo_bytes(32, $cstrong));
        $this->sessionInterface->set('tokenCSRF', $tokenCSRF);

        return $tokenCSRF;
    }

    /**
     * Check the validity of the CSRF token
     *
     * @param string $requestTokenCSRF The CSRF token in the request
     * @return JsonResponse|null
     */
    public function checkCSRFToken(?string $requestTokenCSRF): ?JsonResponse
    {
        // Check if the CSRF token exist
        if(null === $requestTokenCSRF) {
            
            return new JsonResponse(['message' => 'Token CSRF manquant'], 400);
        }
        // Check if the CSRF token is empty
        if("" === $requestTokenCSRF) {

            return new JsonResponse(['message' => 'Token CSRF vide'], 400);
        }
        // Check if the CSRF token is valid

        if(!$this->isRightTokenCSRF($requestTokenCSRF)){

            return new JsonResponse(['message' => 'Token CSRF incorrect'], 400);
        }

        return null;
    }

    /**
     * Check if it's the right CSRF token
     *
     * @param string $requestTokenCSRF The CSRF token in the request
     * @return bool
     */
    public function isRightTokenCSRF(string $requestTokenCSRF): bool
    {
        // Check if the two CSRF tokens are different
        if($requestTokenCSRF !== $this->sessionInterface->get('tokenCSRF')) {

            dump('token CRSF incorrect');
            return false;
        }

        dump('token CSRF correct');
        return true;
    }
    
}