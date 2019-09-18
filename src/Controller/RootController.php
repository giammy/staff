<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class RootController extends AbstractController
{
    /**
     * @Route("/", name="root")
     */
    public function rootIndex()
    {
        return $this->render('index.html.twig', [
            'controller_name' => 'RootController',
        ]);
    }

}
