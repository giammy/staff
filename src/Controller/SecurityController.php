<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Psr\Log\LoggerInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils, LoggerInterface $appLogger)
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();
         
        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        $appLogger->info("IN: loginAction: lastUsername='" . $lastUsername . "' lastError='" . $error . "'");
         
        return $this->render('login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

   /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request, LoggerInterface $appLogger) {
        $appLogger->info("IN: logoutAction username='" . 
            $this->get('security.token_storage')->getToken()->getUser()->getUsername() . "'");
        return $this->redirect($this->generateUrl('home'));
    }


//

   /**
     * @Route("/jsonlogin", name="jsonlogin")
     */


// @Route("/jsonlogin", name="jsonlogin", methods={"POST"})

    public function jsonlogin(Request $request, AuthenticationUtils $authUtils, LoggerInterface $appLogger)
    {
        $user = $this->getUser();

	$lastUsername = $authUtils->getLastUsername();

var_dump($user);
var_dump($lastUsername);

//        $appLogger->info("IN: jsonloginAction: username=" . $user->getUsername() . " roles='" . $user->getRoles() . "'");

        return $this->json([
            'username' => $lastUsername, // "xx", // $user->getUsername(),
            'roles' => "rr", //$user->getRoles(),
        ]);
    }

}
