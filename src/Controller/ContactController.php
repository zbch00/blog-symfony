<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    private ContactRepository $contactRepository;

    /**
     * @param ContactRepository $contactRepository
     */
    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    #[Route('/contact', name: 'app_contact',methods: ['GET','POST'], priority: 1
    )]
    public function index(Request $request): Response
    {
        $contact = new Contact();
        // Création du formulaire
        $formContact = $this->createForm(ContactType::class,$contact);

        //Reconnaitre si le formulaire a été soumis ou pas
        $formContact->handleRequest($request);
        // Est ce que le formulaire a été soumis
        if ($formContact->isSubmitted() && $formContact->isValid()){
            $contact->setNom($contact->getNom());
            $contact->setPrenom($contact->getPrenom());
            $contact->setAdresseEmail($contact->getAdresseEmail());
            $contact->setSujet($contact->getSujet());
            $contact->setDescription($contact->getDescription());
            // Insérer l'article dans la base de données
            $this->contactRepository->add($contact,true);
            return $this->redirectToRoute('app_contact');
        }

        // Appel de la vue twig permettant d'afficher le formulaire
        return $this->renderForm('contact/nouveauContact.html.twig', [
            "formContact" => $formContact
        ]);


    }
}
