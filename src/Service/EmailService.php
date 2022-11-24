<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;


class EmailService
{
    private MailerInterface $mailer;

    /**
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param string $emetteur
     * @param string $destinataire
     * @param string $objet
     * @param string $template
     * @param array $contexte
     * @return void
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */

    public function envoyerEmail(string $emetteur, string $destinataire, string $objet, string $template, array $contexte): void {

     // Créer le mail
        $email= new TemplatedEmail();
        $email->from($emetteur);
        $email->to($destinataire);
        $email->subject($objet);
        $email->htmlTemplate($template);
        $email->context($contexte);
        //Envoyer le mail
        $this->mailer->send($email);;

    }

}