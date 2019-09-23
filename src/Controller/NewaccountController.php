<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Staff;

class NewaccountController extends AbstractController
{
    /**
     * @Route("/newaccount", name="newaccount")
     */
    public function newaccountIndex()
    {
        return $this->render('newaccount/index.html.twig', [
            'controller_name' => 'NewaccountController',
        ]);
    }

    /**
     * @Route("/newaccount/showall", name="newaccountshowall")
     */
    public function newaccountShowallIndex()
    {
        $repo = $this->getDoctrine()->getRepository(Staff::class);

	return $this->render('newaccount/showall.html.twig', [
            'controller_name' => 'NewaccountShowallController',
            'staffList' => $repo->findAll(),
            ]);
    }


}
