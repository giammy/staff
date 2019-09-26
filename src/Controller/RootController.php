<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Entity\Staff;
use App\Entity\Account;

class RootController extends AbstractController
{
    private $params;
    public function __construct(ParameterBagInterface $params) {
        $this->params = $params;
    }

    /**
     * @Route("/", name="root")
     */
    public function rootIndex()
    {
        return $this->render('index.html.twig', [
            'controller_name' => 'RootController',
            'username' => $this->get('security.token_storage')->getToken()->getUser()->getUsername(),
        ]);
    }

    /**
     * @Route("/showall", name="showall")
     */
    public function showallIndex()
    {
        $username = $this->get('security.token_storage')->getToken()->getUser()->getUsername();
	$allowedUsers = preg_split('/, */', $this->params->get('users_ufficio_personale'));
        if (in_array($username, $allowedUsers)) {
            $repo = $this->getDoctrine()->getRepository(Staff::class);
	    return $this->render('showall.html.twig', [
                'controller_name' => 'ShowallController',
                'list' => $repo->findAll(),
                ]);
        } else {
            return $this->redirectToRoute('root');
        }
    }


}
