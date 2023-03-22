<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class SendMail
{
    private $mailerInterface;
    private $clientEmail;
    private $siteHost;


    public function __construct(MailerInterface $mailerInterface, $clientEmail, string $siteHost)
    {
        $this->mailerInterface = $mailerInterface;
        $this->clientEmail = $clientEmail;
        $this->siteHost = $siteHost;
    }

    /**
     * Create and send a mail about "contact" form 
     *
     * @param string $email The user's email
     * @param string $firstname The user's firstname
     * @param string $lastname The user's lastname
     * @param string $subject The subject of the mail
     * @param string $text The text of the mail
     * @return bool
     */
    public function sendContactMail(string $email, string $firstname, string $lastname, string $subject, string $text): bool
    {

        $mail = (new TemplatedEmail())
            ->from($email)
            ->to($this->clientEmail)
            ->subject($subject)
            ->htmlTemplate('mails/mail_contact.html.twig')
            ->context([
                'userFirstname' => $firstname,
                'userLastname' => $lastname,
                'textMail' => $text
            ]);

        try{
            $this->mailerInterface->send($mail);
            return true;
        } catch(TransportExceptionInterface $error){
            // TODO gestion de l'erreur 
            return false;
        } 
    }

    /**
     * Send a mail about buying a surprise card
     *
     * @param [type] $email The user's email
     * @param [type] $firstname The user's firstname
     * @param [type] $lastname The user's lastname
     * @param [type] $amount The amount of the card
     * @return bool
     */
    public function sendCardMail($email, $firstname, $lastname, $amount, $pathPDF): bool
    {
        $mail = (new TemplatedEmail())
        ->from('les-tuileries@admin.com')
        ->to($email)
        ->subject("Carte cadeau restaurant 'Les Tuileries'.")
        ->attachFromPath($pathPDF)
        ->htmlTemplate('mails/mail_card.html.twig')
        ->context([
            'userFirstname' => $firstname,
            'userLastname' => $lastname,
            'amount' => $amount
        ]);

    try{
        $this->mailerInterface->send($mail);
        return true;
    } catch(TransportExceptionInterface $error){
        // TODO gestion de l'erreur 
        return false;
    } 
    }
}
