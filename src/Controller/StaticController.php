<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ContactType;
use App\Form\CommentaireType;
use Symfony\Component\HttpFoundation\Request;

class StaticController extends AbstractController
{
    #[Route('/accueil', name: 'accueil')]
    public function accueil(): Response
    {
        return $this->render('static/accueil.html.twig', []);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(Request $request,\Swift_Mailer $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $nom = $form->get('nom')->getData();
                $sujet = $form->get('sujet')->getData();
                $contenu = $form->get('message')->getData();
                $this->addFlash('notice','Bouton appuyé');
                $message = (new \Swift_Message($form->get('sujet')->getData()))
                ->setFrom($form->get('email')->getData())
                ->setTo('imranekent@gmail.com')
                //->setBody($form->get('message')->getData());
                ->setBody($this->renderView('emails/contact-email.html.twig',array(
                'nom'=>$nom,
                'sujet'=>$sujet,
                'contenu'=>$contenu)),'text/html');
                $mailer->send($message);
                return $this->redirectToRoute('contact');
            }
        }
        return $this->render('static/contact.html.twig', ['form' => $form -> createView()]);
    }

    #[Route('/commentaire', name: 'commentaire')]
    public function commentaire(Request $request): Response
    {
        $form = $this->createForm(CommentaireType::class);
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $nom = $form->get('nom')->getData();
                $this->addFlash('notice','Bouton appuyé ' . $nom);
            }
        }
        return $this->render('static/commentaire.html.twig', ['form' => $form -> createView()]);
    }
}
