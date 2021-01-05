<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Account;
use App\Repository\ActeurRepository;

class PublicController extends AppController{

    /**
     * @Route("/public/legal", name="public.legal")
     */
    public function legal(SessionInterface $session){
        $subtitle = 'Mention légales';
        $title = "GBAF - " . $subtitle;
        $message = "";
        $headerText = '<a href="login">Connexion</a>';
        $hideBtn = true;
        $logged = $this->logged($session);
        $user = new Account();
        if($logged){
            // récupération des données utilisateur
            $repo = $this->getDoctrine()->getRepository(Account::class);             
            $response = $repo->findOneByIdUser($session->get('auth'));
            if($response){
                $user = $response;
                $hideBtn = false;
                $headerText = $this->secure($user->getPrenom()) . ' ' . $this->secure($user->getNom());
            }
        }
        return $this->render('public/legal.html.twig', [
            'title' => $title,
            'message' => $message,
            'headerText' => $headerText,
            'hideBtn' => $hideBtn,
            'logged' => $logged,
            'user' => $user
        ]);
    }

    /**
     * @Route("/public/contact", name="public.contact")
     */
    public function contact(SessionInterface $session){
        $subtitle = 'Contact';
        $title = "GBAF - " . $subtitle;
        $message = "Aucun code: TODO";
        $headerText = '<a href="login">Connexion</a>';
        $hideBtn = true;
        $logged = $this->logged($session);
        $user = new Account();
        if($logged){
            // récupération des données utilisateur
            $repo = $this->getDoctrine()->getRepository(Account::class);             
            $response = $repo->findOneByIdUser($session->get('auth'));
            if($response){
                $user = $response;
                $hideBtn = false;
                $headerText = $this->secure($user->getPrenom()) . ' ' . $this->secure($user->getNom());
            }
        }
        return $this->render('public/contact.html.twig', [
            'title' => $title,
            'message' => $message,
            'headerText' => $headerText,
            'hideBtn' => $hideBtn,
            'logged' => $logged,
            'user' => $user
        ]);
    }

    /**
     * @Route("/public/notFound", name="public.notFound")
     */
    public function notFound(SessionInterface $session){
        $subtitle = 'Page introuvable';
        $title = "GBAF - " . $subtitle;
        $message = "";
        $headerText = '<a href="login">Connexion</a>';
        $hideBtn = true;
        $logged = $this->logged($session);
        $user = new Account();
        if($logged){
            // récupération des données utilisateur
            $repo = $this->getDoctrine()->getRepository(Account::class);             
            $response = $repo->findOneByIdUser($session->get('auth'));
            if($response){
                $user = $response;
                $hideBtn = false;
                $headerText = $this->secure($user->getPrenom()) . ' ' . $this->secure($user->getNom());
            }
        }
        return $this->render('public/notFound.html.twig', [
            'title' => $title,
            'message' => $message,
            'headerText' => $headerText,
            'hideBtn' => $hideBtn,
            'logged' => $logged,
            'user' => $user
        ]);
    }
}