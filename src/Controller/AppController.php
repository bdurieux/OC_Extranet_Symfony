<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Account;

class AppController extends AbstractController{

    /**
	*	vérifie le mot de passe fourni avec celui en base de donnée
	*	@param $username
	*	@param $password
	*	@return boolean renvoie true si le mot de passe correspond
	*/
	public function checkLogin($username, $password, $session){
        $repo = $this->getDoctrine()->getRepository(Account::class);
        $user = new Account();
        $response = $repo->findOneByUsername($username);
        if($response){
            $user = $response;
        }
		if ($user->getIdUser() !== null) {
			if(password_verify($password,$user->getPassword())){
				$session->set('auth',$user->getIdUser());
				return true;
			}			
		}
		return false;
    }
    
    /**
	 * vérifie si l'utilisateur est connecté
	 */
	public function logged(SessionInterface $session){
		return $session->get('auth') !== null;
    }
    
    /**
	 * nettoie une chaine de caractère
	 * @return
	 */
	protected function secure($data){
		if(is_string($data)){			
			$data = htmlspecialchars(htmlspecialchars(stripslashes(trim($data))));
		}
        return $data;
    }
    
	/**
	 * formate un texte en ajoutant des <li></li> si un ':' est suivi de ';'
	 * @param $text le texte à formatter
	 * @return
	 */
	protected function formatText($text){
		$html = "";
		$pattern = "#:+(.+;+).+#";
		if (preg_match($pattern, $text)){
			// on décompose le text en phrase
			$parts = explode(".",$text);
			foreach($parts as $part){		
				if(preg_match($pattern, $part)){
					$pieces = explode(':',$part);
					$debut = $pieces[0];
					$reste = substr($part, strlen($debut)+1);
					$ul = explode(';', $reste);
					$html .= $debut . '<ul>';
					foreach ($ul as $li) {
						$html .= '<li>' . $li . '</li>';
					}
					$html .= '</ul>';
				}else{
					$html .= $part . '.';
				}
			}
		}else{
			$html = $text;
		}
		return $html;
	}
}