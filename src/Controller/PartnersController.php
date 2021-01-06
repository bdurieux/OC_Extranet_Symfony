<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Account;
use App\Entity\Acteur;
use App\Entity\Post;
use App\Entity\Vote;
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
                        $entityManager = $this->getDoctrine()->getManager();
                        $comment = new Post();
                        $comment->setIdUser($user->getIdUser())
                            ->setIdActeur($partner->getIdActeur())
                            ->setPost($this->secure($_POST['comment']))
                            ->setDateAdd(new \DateTime('now'));
                        $entityManager->persist($comment);
                        $entityManager->flush();
                        return $this->redirectToRoute('acteur', array('id' => $partner->getIdActeur()));
                    }else{
                        $message = "Vous avez déjà commenté cet acteur.";
                    }                    
                }                
            }elseif(isset($_POST['like'])){ // ajout d'un like
                if(!$this->saveVote($user->getIdUser(), $partner->getIdActeur(),true,$voteRepo)){
                    $message = "Vous avez déjà voté pour  cet acteur.";
                }
            }elseif(isset($_POST['dislike'])){  // ajout d'un dislike
                if(!$this->saveVote($user->getIdUser(), $partner->getIdActeur(),false,$voteRepo)){
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
    }

    /**
     * appel la fonction qui sauve la vote en bdd et retourne false si le vote existe deja
     * @param $id_user 
     * @param $id_acteur
     * @param $like 
     * @return false si 1 vote associé à id_user & id_acteur existe deja
     */
    private function saveVote($id_user,$id_acteur,bool $like,VoteRepository $voteRepo){
        $existeDeja = false;
        $value = 1;
        if(!$like){
            $value = -1;
        }
        // vérifier l'unicité du vote
        if(empty($voteRepo->findBy(array('idUser' => $id_user,'idActeur' => $id_acteur)))){
            //sauver le vote en bdd 
            $entityManager = $this->getDoctrine()->getManager();
            $vote = new vote();
            $vote->setIdUser($id_user)
                ->setIdActeur($id_acteur)
                ->setVote($value);
            $entityManager->persist($vote);
            $entityManager->flush();            
            /* $sql = 'INSERT INTO vote (id_user, id_acteur, vote) VALUES (?,?,?)';
            $request = $bdd->prepare($sql);
            $request->execute(array($id_user,$id_acteur,$value)); */
            $existeDeja = true;
        }
        return $existeDeja;
    }
}