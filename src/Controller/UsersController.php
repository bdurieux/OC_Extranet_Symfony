<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Account;

class UsersController extends AppController{

    /**
     * @Route("/", name="login")
     */
    public function login(SessionInterface $session){
        $subtitle = 'Connexion';
        $title = "GBAF - " . $subtitle;
        $message = "";
        $headerText = '<a href="inscription">Inscription</a>';
        $hideBtn = true;
        $logged = $this->logged($session);
        if (!empty($_POST)) {
			//vérification des identifiants
			if ($this->checkLogin($this->secure($_POST['username']),$this->secure($_POST['password']),$session)) {		
                return $this->redirectToRoute('home');
			}else{
                $message = "Identifiants incorrects!";
            }
        }
        return $this->render('users/login.html.twig', [
            'title' => $title,
            'message' => $message,
            'headerText' => $headerText,
            'hideBtn' => $hideBtn,
            'logged' => $logged
        ]);
    }

    /**
     * @Route("/forgotPass", name="forgotPass")
     */
    public function forgotPass(){
        $subtitle = 'Mot de passe oublié';
        $title = "GBAF - " . $subtitle;
        $message = "";
        $headerText = '<a href="inscription">Inscription</a>';
        $hideBtn = true;
        $logged = false;
        $hideQuestion = true;
        $question = "Entrez votre pseudo et répondez à la question.";
        if (!empty($_POST)) {
            // on récupère les données de l'utilisateur en bdd d'après son username
            $repo = $this->getDoctrine()->getRepository(Account::class);
            $user = new Account(); 
            $response = $repo->findOneByUsername($this->secure($_POST['username']));
            if($response){
                $user = $response;
            }	
            if($user->getIdUser() !== null){
                $question = $user->getQuestion();
                $hideQuestion =false;
                if(isset($_POST['reponse'])){
                    // vérification des identifiants
                    if (password_verify($this->secure($_POST['reponse']),$user->getReponse())) {
                        return $this->redirectToRoute('newPass');
                    }else{
                        $message = "Identifiants incorrects.";
                    }
                }        
            }else{
                $message = "Pseudo inconnu.";
            }			
        }
        return $this->render('users/forgotPass.html.twig', [
            'title' => $title,
            'message' => $message,
            'headerText' => $headerText,
            'hideBtn' => $hideBtn,
            'hideQuestion' => $hideQuestion,
            'question' => $question,
            'logged' => $logged
        ]);
    }

    /**
     * @Route("/newPass", name="newPass")
     */
    public function newPass(){
        $subtitle = 'Nouveau mot de passe';
        $title = "GBAF - " . $subtitle;
        $message = "";
        $headerText = '<a href="inscription">Inscription</a>';
        $hideBtn = true;
        $logged = false;
        if(!empty($_POST)){		
            // on récupère les données de l'utilisateur en bdd d'après son username	
            $repo = $this->getDoctrine()->getRepository(Account::class);
            $user = new Account(); 
            $response = $repo->findOneByUsername($this->secure($_POST['username']));
            if($response){
                $user = $response;
            }	
            if($user->getIdUser() !== null){			
                if(strlen($this->secure($_POST['password1']))>0 && 
                    $this->secure($_POST['password1']) === $this->secure($_POST['password2'])){
                    //faire l'update du password
                    $user->setPassword(password_hash($this->secure($_POST['password1']),PASSWORD_DEFAULT));
                    var_dump('TODO : UPDATE du password');
                    /* $sql = 'UPDATE account SET password = ? WHERE id_user = ?';
                    $request = $bdd->prepare($sql);
                    $request->execute(array(password_hash(secure($_POST['password1']),PASSWORD_DEFAULT),$user['id_user']));
                     */
                    return $this->redirectToRoute('login');
                }else{
                    $message = "Les 2 mots de passe ne correspondent pas.";
                }
            }else{
                $message = "Pseudo inconnu.";
            }
        }
        return $this->render('users/newPass.html.twig', [
            'title' => $title,
            'message' => $message,
            'headerText' => $headerText,
            'hideBtn' => $hideBtn,
            'logged' => $logged
        ]);
    }

    /**
     * @Route("/inscription", name="inscription")
     */
    public function inscription(){
        $subtitle = 'Inscription';
        $title = "GBAF - " . $subtitle;
        $message = "";
        $headerText = '<a href="login">Connexion</a>';
        $hideBtn = true;
        $logged = false;
        if(!empty($_POST)){
            $message = checkParam($_POST);
            if(empty($message)){
                // vérification de l'unicité du pseudo
                $repo = $this->getDoctrine()->getRepository(Account::class);
                $user = new Account(); 
                $response = $repo->findOneByUsername($this->secure($_POST['username']));
                if($response){
                    $user = $response;
                }	
                if($user->getIdUser() !== null){
                    $message = "Pseudo déjà utilisé.";
                }else{
                    //TODO : INSERT 
                    /* $sql = 'INSERT INTO account (nom, prenom, username, password, question, reponse) 
                            VALUES (?,?,?,?,?,?)';
                    $request = $bdd->prepare($sql);
                    $request->execute(array(
                        secure($_POST['nom']),
                        secure($_POST['prenom']),
                        secure($_POST['username']),
                        password_hash(secure($_POST['password']),PASSWORD_DEFAULT),
                        secure($_POST['question']),
                        password_hash(secure($_POST['reponse']),PASSWORD_DEFAULT)
                    )); */
                    return $this->redirectToRoute('login');
                }				
            }
        }
        return $this->render('users/inscription.html.twig', [
            'title' => $title,
            'message' => $message,
            'headerText' => $headerText,
            'hideBtn' => $hideBtn,
            'logged' => $logged
        ]);
    }

    /**
     * @Route("/param", name="param")
     */
    public function param(SessionInterface $session){
        $subtitle = 'Inscription';
        $title = "GBAF - " . $subtitle;
        $message = "";
        $headerText = '<a href="login">Connexion</a>';
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
            if(!empty($_POST)){
                $message = checkParam($_POST);
                if(empty($message)){
                    $pseudoUser = new Account();
                    $pseudoUser = $repo->findUserByUsername($this->secure($_POST['username']));
                    // on vérifie que le pseudo n'est pas deja utilisé par un autre user
                    if($pseudoUser->getIdUser() !== null && ($pseudoUser->getIdUser() != $user->getIdUser())){
                        $message .= "Pseudo déjà utilisé.";
                    }else{
                        // TODO: UPDATE
                        /* $sql = 'UPDATE account SET nom = ?, prenom = ?, username = ?, password = ?, question = ?, reponse = ? 
                        WHERE id_user = ?';
                        $request = $bdd->prepare($sql);
                        $request->execute(array(
                            secure($_POST['nom']),
                            secure($_POST['prenom']),
                            secure($_POST['username']),
                            password_hash(secure($_POST['password']),PASSWORD_DEFAULT),
                            secure($_POST['question']),
                            password_hash(secure($_POST['reponse']),PASSWORD_DEFAULT),
                            $user['id_user']
                        )); */
                        return $this->redirectToRoute('login');
                    }				
                }
            }
        }else{
            return $this->redirectToRoute('login');
        }
        return $this->render('users/param.html.twig', [
            'title' => $title,
            'message' => $message,
            'headerText' => $headerText,
            'hideBtn' => $hideBtn,
            'logged' => $logged,
            'user' => $user
        ]);
    }

    /**
     * ferme la session en cours et redirige vers la page de connexion
     * @Route("/logout", name="logout")
     */
    public function logout(SessionInterface $session){
        //unset($_SESSION['auth']);
        $session->clear();
		return $this->redirectToRoute('login');
    }

    /**
     * @Route("/chat", name="chat")
     */
    public function chat(){
        // TODO
        return new Response('Page du chat');
    }

    /**
	 * vérifie la validité des paramètres du compte
	 * @param param
	 * @return array renvoie un tableau contenant $message et $errors
	 */
	protected function checkParam($params){
		$errors = false;
		$message = "";
		/* if(isset($_FILES['avatar']) && $_FILES['avatar']['size']>1000000){
			$message .= "Taille de l'avatar trop grande (max 1 Mo) <br>";
			$errors = true;
		} */
		if(isset($_POST['nom']) && strlen($this->secure($_POST['nom']))<2){
			$message .= "Nom invalide <br>";
			$errors = true;
		}
		if(isset($_POST['prenom']) && strlen($this->secure($_POST['prenom']))<2){
			$message .= "Prénom invalide <br>";
			$errors = true;
		}
		if(isset($_POST['username']) && strlen($this->secure($_POST['username']))<4){
			$message .= "Pseudo invalide <br>";
			$errors = true;
		}
		if(isset($_POST['password']) && strlen($this->secure($_POST['password']))<4){
			$message .= "Mot de passe invalide <br>";
			$errors = true;
		}
		if(isset($_POST['question']) && strlen($this->secure($_POST['question']))<10){
			$message .= "Question invalide <br>";
			$errors = true;
		}
		if(isset($_POST['reponse']) && strlen($this->secure($_POST['reponse']))<4){
			$message .= "Réponse invalide <br>";
			$errors = true;
		}
		return compact('message','errors');
	}
}