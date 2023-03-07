<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FrontHomeController extends AbstractController
{
    #[Route('/', name: 'app_front')]
    #[Route('/front/home', name: 'app_front_home')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $contactFormData = $form->getData();
            
            $message = (new TemplatedEmail())
                // ->from($contactFormData['email'])
                ->from(new Address($contactFormData['email']))
                ->to('contact@jmlbzz.com')
                ->subject('Vous avez reçu un mail de '.$contactFormData['name'])
                ->htmlTemplate("contact/contactmail.html.twig")
                ->context([
                    "mail"=>$contactFormData['email'],
                    "name"=>$contactFormData['name'],
                    "subject"=>$contactFormData['subject'],
                    "message"=>$contactFormData['message'],
                ]);

            $mailer->send($message);

            //$this->addFlash('success', $message->toString());
            $this->addFlash('success', 'Votre message a bien été envoyé');

            return $this->redirectToRoute('app_front');
        }


        return $this->render('front_home/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
