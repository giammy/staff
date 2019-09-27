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
    public function loginAction(Request $request, AuthenticationUtils $authUtils, LoggerInterface $logger)
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();
         
        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        $logger->info("IN: loginAction: lastUsername='" . $lastUsername . "' lastError='" . $error . "'");
         
        return $this->render('login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

   /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request, LoggerInterface $logger) {
        $logger->info("IN: logoutAction");
        return $this->redirect($this->generateUrl('root'));
    }


}
