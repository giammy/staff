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
     * @Route("/public", name="public")
     */
    public function publicAction(LoggerInterface $appLogger)
    {
        $username = $this->get('security.token_storage')->getToken()->getUser(); //->getUsername();
	if ($username != 'anon.') {
            $username = $username->getUsername();
        }
        $appLogger->info("IN: publicAction: username='" . $username . "' allowed");
        $dateNow = new \DateTime();
	return $this->render('public/index.html.twig', [
            'controller_name' => 'PublicController',
            'username' => $username,
            ]);
    }

    /**
     * @Route("/public/agenda", name="publicAgenda")
     */
    public function publicAgendaAction(LoggerInterface $appLogger)
    {
        $username = $this->get('security.token_storage')->getToken()->getUser(); //->getUsername();
	if ($username != 'anon.') {
            $username = $username->getUsername();
        }
        $appLogger->info("IN: publicAgendaAction: username='" . $username . "' allowed");
        $dateNow = new \DateTime();
        $repo = $this->getDoctrine()->getRepository(Staff::class);
	return $this->render('public/agenda.html.twig', [
            'controller_name' => 'PublicAgendaController',
            'list' => array_filter($repo->findAll(), function ($x) use ($dateNow) { 
                $valid = $x->getValidTo();
                return (($x->getName() != "noname") && ($valid >= $dateNow)); 
            }),
            'username' => $username,
            ]);
    }

    /**
     * @Route("/public/organization/{group}", name="publicOrganization")
     */
    public function publicOrganizationAction(LoggerInterface $appLogger, $group="")
    {
        $username = $this->get('security.token_storage')->getToken()->getUser(); //->getUsername();
	if ($username != 'anon.') {
            $username = $username->getUsername();
        }
        $appLogger->info("IN: publicAgendaAction: username='" . $username . "' allowed");
        $dateNow = new \DateTime();
        $repo = $this->getDoctrine()->getRepository(Staff::class);
        $listAll = array_filter($repo->findAll(), function ($x) use ($dateNow) { 
                $valid = $x->getValidTo();
                return (($x->getName() != "noname") && ($valid >= $dateNow)); 
            });
	if ($group != "") {
            $listAll = array_filter($listAll, function ($x) use ($group) { 
                return (($x->getGroupName() == $group) || ($x->getLeaderOfGroup() == $group)); 
            });
        }

	return $this->render('public/organization.html.twig', [
            'controller_name' => 'PublicOrganizationController',
            'list' => $listAll,
            'username' => $username,
            ]);
    }


}
