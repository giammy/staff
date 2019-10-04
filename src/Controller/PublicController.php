<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Log\LoggerInterface;
use App\Entity\Staff;
use App\Entity\Account;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class PublicController extends AbstractController
{
    private $params;
    public function __construct(ParameterBagInterface $params) {
        $this->params = $params;
    }

    /**
     * @Route("/public/agenda", name="publicagenda")
     */
    public function publicAgendaAction(LoggerInterface $appLogger)
    {
        $username = $this->get('security.token_storage')->getToken()->getUser()->getUsername();
        $appLogger->info("IN: publicAgendaAction: username='" . $username . "' allowed");
        $dateNow = new \DateTime();
        $repo = $this->getDoctrine()->getRepository(Staff::class);
	return $this->render('public/agenda.html.twig', [
            'controller_name' => 'PublicAgendaController',
            'list' => array_filter($repo->findAll(), function ($x) use ($dateNow) { 
                $valid = $x->getValidTo();
                return (($x->getName() != "noname") && ($valid >= $dateNow)); 
            }),
            'username' => $this->get('security.token_storage')->getToken()->getUser()->getUsername(),
            ]);
    }

}
