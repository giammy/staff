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

class RootController extends AbstractController
{
    private $params;
    public function __construct(ParameterBagInterface $params) {
        $this->params = $params;
    }

    /**
     * @Route("/", name="home")
     */
    public function homeAction(LoggerInterface $logger)
    {
        $logger->info("IN: homeAction:");

        return $this->render('index.html.twig', [
            'controller_name' => 'HomeController',
            'username' => $this->get('security.token_storage')->getToken()->getUser()->getUsername(),
        ]);
    }

    /**
     * @Route("/showall", name="showall")
     */
    public function showallAction(LoggerInterface $logger)
    {
        $logger->info("IN: showallAction:");

        $username = $this->get('security.token_storage')->getToken()->getUser()->getUsername();
	$allowedUsers = preg_split('/, */', $this->params->get('users_ufficio_personale'));
        if (in_array($username, $allowedUsers)) {
            $logger->info("IN: showallAction: username='" . $username . "' allowed");
            $repo = $this->getDoctrine()->getRepository(Staff::class);
	    return $this->render('showall.html.twig', [
                'controller_name' => 'ShowallController',
                'list' => $repo->findAll(),
                'username' => $this->get('security.token_storage')->getToken()->getUser()->getUsername(),
                ]);
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/editUser", name="editUser")
     */
    public function editUserAction(Request $request, \Swift_Mailer $mailer, LoggerInterface $logger)
    {
        $logger->info("IN: editUserAction");
        $account = new Staff();
        $account->setCreated(new \DateTime(date('Y-m-d H:i:s')));

        $form = $this->createFormBuilder($account)
            ->add('username', TextType::class)
            ->add('email', TextType::class)
            ->add('secondaryEmail', TextType::class)
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('groupName', ChoiceType::class, array(
			 'choices'  => array(
			    'AI' => 'GAI',
			    'FA' => 'GFA',
			    'FB' => 'GFB',
	    	            'FC' => 'GFC',
			    'FD' => 'GFD',
			    'FT' => 'GFT',
		      	    'IP' => 'GIP',
		    	    'SE' => 'GSE',
			    'IE' => 'GIE',
			    'SIT' => 'SCA',
			    'DII' => 'DII',
			    'NBI' => 'NBI',
 	       	            'Officine' => 'OME',
       		            'SX-Alimentazioni' => 'SXA',
		            'SX-Controlli' => 'SXC',
     		            'SX-Diagnostiche' => 'SXD',
   		       	    'SX-Macchina' => 'SXM',
		      	    'Direzione' => 'DIR',
		      	    'Amministrazione' => 'AMM',
     			    'Ufficio Manutenzione' => 'SMA',
	  		    'Ufficio Tecnico' => 'UTE',
			    'Altro' => 'BLK',
	  	         ),
	          ))
            ->add('leaderOfGroup', ChoiceType::class, array(
			 'choices'  => array(
			    'AI' => 'GAI',
			    'FA' => 'GFA',
			    'FB' => 'GFB',
	    	            'FC' => 'GFC',
			    'FD' => 'GFD',
			    'FT' => 'GFT',
		      	    'IP' => 'GIP',
		    	    'SE' => 'GSE',
			    'IE' => 'GIE',
			    'SIT' => 'SCA',
			    'DII' => 'DII',
			    'NBI' => 'NBI',
 	       	            'Officine' => 'OME',
       		            'SX-Alimentazioni' => 'SXA',
		            'SX-Controlli' => 'SXC',
     		            'SX-Diagnostiche' => 'SXD',
   		       	    'SX-Macchina' => 'SXM',
		      	    'Direzione' => 'DIR',
		      	    'Amministrazione' => 'AMM',
     			    'Ufficio Manutenzione' => 'SMA',
	  		    'Ufficio Tecnico' => 'UTE',
			    'Altro' => 'BLK',
	  	         ),
	          ))
            ->add('qualification', TextType::class)
            ->add('organization', TextType::class)
            ->add('totalContractualHoursPerYear', IntegerType::class)
            ->add('parttimePercent', NumberType::class)
            ->add('isTimeSheetEnabled', ChoiceType::class, array(
				 'expanded' => true,
				 'multiple' => false,
				 'choices'  => array(
					 'Yes' => true,  
		    	      	         'No' => false,),
		                  'data' => true,
		  	         ))
            ->add('validFrom', DateType::class, array('data' => new \DateTime()))
            ->add('validTo', DateType::class, array('data' => new \DateTime()))
            ->add('note', TextType::class)
            ->add('officePhone', TextType::class)
            ->add('officeMobile', TextType::class)
            ->add('officeLocation', TextType::class)
            ->getForm();

	$form->handleRequest($request);

        $logger->info("IN: editUserAction: isSubmitted=" . ($form->isSubmitted()?"TRUE":"FALSE") . 
                    " isValid=" . ($form->isSubmitted()?($form->isValid()?"TRUE":"FALSE"):"---") );

	if ($form->isSubmitted() && $form->isValid()) {
             // $form->getData() holds the submitted values
             // but, the original variable has also been updated
	     $account = $form->getData();

// TODO se non impostati impostali
//             $account->setValidFrom(new \DateTime(date('Y-m-d H:i:s')));
//             $account->setValidTo(new \DateTime(date('Y-m-d H:i:s')));

             $account->setVersion($this->params->get('staff_current_db_format_version'));

	     // save
	     //$repo = $this->getDoctrine()->getrepository('AppBundle:AccountRequest');
             $em = $this->getDoctrine()->getManager();
             $em->persist($account);
             $em->flush();

// TODO pagina ringraziamento
             return $this->redirectToRoute('home');
    	}

        return $this->render('editUser.html.twig', [
            'form' => $form->createView(),
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

}
