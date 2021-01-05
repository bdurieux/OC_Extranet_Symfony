<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Account;
use App\Entity\Acteur;
use App\Repository\ActeurRepository;
use App\Repository\PostRepository;
use App\Repository\VoteRepository;

class PartnersController extends AppController{

    /**
     * @Route("/partners", name="home")
     */
    public function home(ActeurRepository $acteurRepository, SessionInterface $session){
        $subtitle = 'Accueil';
        $title = "GBAF - " . $subtitle;
        $message = "";
        $headerText = '<a href="inscription">Inscription</a>';
        $hideBtn = true;
        $logged = $this->logged($session);
        if($logged){
            // récupération des données utilisateur
            $repo = $this->getDoctrine()->getRepository(Account::class);
            $user = new Account(); 
            $response = $repo->findOneByIdUser($session->get('auth'));
            if($response){
                $user = $response;
                $hideBtn = false;
                $headerText = $this->secure($user->getPrenom()) . ' ' . $this->secure($user->getNom());
            }
        }else{
            return $this->redirectToRoute('login');
        }
        // récupération des données des acteurs
        //$repoPartner = $this->getDoctrine()->getRepository(Acteur::class);
        $partners = $acteurRepository->findAll();
        return $this->render('partners/home.html.twig', [
            'title' => $title,
            'message' => $message,
            'headerText' => $headerText,
            'hideBtn' => $hideBtn,
            'partners' => $partners,
            'logged' => $logged
        ]);
    }

    /**
     * @Route("/partners/{id}", name="acteur")
     */
    public function show(
            $id,
            SessionInterface $session,
            ActeurRepository $acteurRepository,
            PostRepository $postRepo,
            VoteRepository $voteRepo
        ){
        $subtitle = 'Accueil';
        $title = "GBAF - " . $subtitle;
        $message = "";
        $headerText = '<a href="inscription">Inscription</a>';
        $hideBtn = true;
        $logged = $this->logged($session);
        // on vérifie que l'utilisateur est identifié        
        if($logged){
            // récupération des données utilisateur
            $repo = $this->getDoctrine()->getRepository(Account::class);
            $user = new Account(); 
            $response = $repo->findOneByIdUser($session->get('auth'));
            if($response){
                $user = $response;
                $hideBtn = false;
                $headerText = $this->secure($user->getPrenom()) . ' ' . $this->secure($user->getNom());
            }
        }else{
            return $this->redirectToRoute('login');
        } 
        // on récupère les données du partenaire
        $partner = new Acteur();
        $partner = $acteurRepository->findOneByIdActeur($id);
        // si l'acteur existe
        if($partner !== null){
            // on formate le texte descriptif
            $partner->setDescription($this->formatText($partner->getDescription()));
            if(isset($_POST['comment'])){   // ajout d'un commentaire demandé
                // vérifier validité du commentaire
                if(strlen($this->secure($_POST['comment']))>4){
                    // vérifier l'unicité du commentaire
                    if(empty($postRepo->findBy(array('idUser' => $user->getIdUser(),'idActeur' => $partner->getIdActeur())))){
                        //sauver le commentaire en bdd 
                        $sql = 'INSERT INTO post (id_user, id_acteur, post) 
                            VALUES (?,?,?)';
                        $request = $bdd->prepare($sql);
                        $request->execute(array(
                            $user['id_user'],
                            $partner['id_acteur'],
                            secure($_POST['comment'])
                        ));
                        header('Location: partners.php?id=' . $partner['id_acteur']);
                    }else{
                        $message = "Vous avez déjà commenté cet acteur.";
                    }                    
                }                
            }elseif(isset($_POST['like'])){ // ajout d'un like
                if(!vote($user['id_user'], $partner['id_acteur'],true,$bdd)){
                    $message = "Vous avez déjà voté pour  cet acteur.";
                }
            }elseif(isset($_POST['dislike'])){  // ajout d'un dislike
                if(!vote($user['id_user'], $partner['id_acteur'], false,$bdd)){
                    $message = "Vous avez déjà voté pour  cet acteur.";
                }                
            }
            $nb_like = $voteRepo->countVote($partner->getIdActeur(),true);
            $nb_dislike = $voteRepo->countVote($partner->getIdActeur(),false);
            $comments = $postRepo->findByIdActeur($partner->getIdActeur());
        }else{
            return $this->redirectToRoute('public.notFound');
        }
        return $this->render('partners/show.html.twig', [
            'title' => $title,
            'message' => $message,
            'headerText' => $headerText,
            'hideBtn' => $hideBtn,
            'partner' => $partner,
            'logged' => $logged,
            'nb_like' => $nb_like,
            'nb_dislike' => $nb_dislike,
            'comments' => $comments
        ]);
        //return new Response('Page de partenaire ' . $id);
    }
}