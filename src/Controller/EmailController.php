<?php

namespace App\Controller;


use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class EmailController extends AbstractController
{
    #[Route('/email', name: 'app_email')]
    public function index(EmailService $emailService): Response
    {
        $emailService->envoyerEmail("emetteur@test.fr","destinataire@test.fr","coucou boubou","email/email.html.twig",['prenom'=>'Jean','nom'=>'OUUU']);
        return $this->redirectToRoute("app_articles");
    }




}
