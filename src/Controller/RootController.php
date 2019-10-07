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
    public function homeAction(LoggerInterface $appLogger)
    {
        $appLogger->info("IN: homeAction:");

        return $this->render('index.html.twig', [
            'controller_name' => 'HomeController',
            'username' => $this->get('security.token_storage')->getToken()->getUser()->getUsername(),
        ]);
    }

    /**
     * @Route("/showall/{item}", name="showall")
     */
    public function showallAction(LoggerInterface $appLogger, $item="0")
    {
        $appLogger->info("IN: showallAction:");

	$item=intval($item);

        $username = $this->get('security.token_storage')->getToken()->getUser()->getUsername();
	$allowedUsers = preg_split('/, */', $this->params->get('users_ufficio_personale'));
        if (in_array($username, $allowedUsers)) {
            $appLogger->info("IN: showallAction: username='" . $username . "' allowed");
            $repo = $this->getDoctrine()->getRepository(Staff::class);
            $dateNow = new \DateTime();
	    return $this->render('showall.html.twig', [
                'controller_name' => 'ShowallController',
                'list' => $repo->findAll(),
                'list' => array_filter($repo->findAll(), function ($x) use ($dateNow, $item) { 
                    if ($item == -1) {
                        return true;
                    } else {
                        $valid = $x->getValidTo();
                        return (($x->getName() != "noname") && ($valid >= $dateNow)); 
                    }
                }),
                'username' => $this->get('security.token_storage')->getToken()->getUser()->getUsername(),
                ]);
        } else {
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/editUser/{id}", name="editUser")
     */
    public function editUserAction(Request $request, \Swift_Mailer $mailer, LoggerInterface $appLogger, $id="-1")
    {
        $appLogger->info("IN: editUserAction");
        $id = intval($id);
        $repo = $this->getDoctrine()->getRepository(Staff::class);

        // if id == -1 -> new user, else edit id user TODO
	$account = $repo->find($id);
        $oldAccount = $account;
        if (!$account) {
            // id does not exist: create new user
            $account = new Staff();
            $account->setCreated(new \DateTime(date('Y-m-d H:i:s')));
        }

        $form = $this->createFormBuilder($account)
            ->add('username', TextType::class, array(
                         'required' => false,))
            ->add('email', TextType::class, array(
                         'required' => false,))
            ->add('secondaryEmail', TextType::class, array(
                         'required' => false,))
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('groupName', ChoiceType::class, array(
                         //'expanded' => false,
                         //'multiple' => false,
                         'placeholder' => 'Scegliere un gruppo',
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
                         'required' => false,
                         'placeholder' => 'Se capogruppo, indicare il gruppo',
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
            ->add('qualification', ChoiceType::class, array(
                         'placeholder' => 'Scegliere la qualifica',
			 'choices'  => array(
			    'AMM' => 'AMM',
			    'ASG' => 'ASG',
			    'COL' => 'COL',
	    	            'DOT' => 'DOT',
			    'PRF' => 'PRF',
			    'PRO' => 'PRO',
		      	    'RC1' => 'RC1',
		    	    'RC2' => 'RC2',
			    'RC3' => 'RC3',
			    'TEC' => 'TEC',
	  	         ),
	          ))
            ->add('organization', ChoiceType::class, array(
                         'placeholder' => "Scegliere l'ente",
			 'choices'  => array(
			    'CNR' => 'CNR',
			    'ENEA' => 'ENE',
			    'INFN' => 'INF',
	    	            'RFX' => 'RFX',
			    'UNIPD' => 'UPD',
	  	         ),
	          ))
            ->add('totalContractualHoursPerYear', IntegerType::class, array(
	                         //'data' => '1720',
	                        ))
            ->add('parttimePercent', IntegerType::class, array(
	                         //'data' => '100',
                                ))
            ->add('isTimeSheetEnabled', ChoiceType::class, array(
				 'expanded' => true,
				 'multiple' => false,
				 'choices'  => array(
					 'Yes' => true,  
		    	      	         'No' => false,),
		                  //'data' => null,
		  	         ))
            ->add('validFrom', DateType::class, array('data' => new \DateTime()))
            ->add('validTo', DateType::class, array(
                                  'years' => range(date('Y')-1, date('Y')+100),
                                  'data' => new \DateTime('2099-12-31 11:59:59'),

                                 ))
            ->add('note', TextType::class, array(
                         'required' => false,))
            ->add('officePhone', TextType::class, array(
                         'required' => false,))
            ->add('officeMobile', TextType::class, array(
                         'required' => false,))
            ->add('officeLocation', TextType::class, array(
                        'required' => false,))
            ->getForm();

	$form->handleRequest($request);

        $appLogger->info("IN: editUserAction: isSubmitted=" . ($form->isSubmitted()?"TRUE":"FALSE") . 
                    " isValid=" . ($form->isSubmitted()?($form->isValid()?"TRUE":"FALSE"):"---") );

	if ($form->isSubmitted() && $form->isValid()) {
             // $form->getData() holds the submitted values
             // but, the original variable has also been updated
	     $account = $form->getData();
             $account->setTotalHoursPerYear(($account->getTotalContractualHoursPerYear()*
                                             $account->getParttimePercent())/100);
	     $account->setLastChangeAuthor($this->get('security.token_storage')->getToken()->getUser()->getUsername());
	     $account->setLastChangeDate(new \Datetime());

// GMY - TODO
             if ($oldAccount == null) { // new entry
                 $account->setVersion($this->params->get('staff_current_db_format_version'));
             } else {
                 // change validity dates? duplicate? store new version?
             }

	     // save
	     //$repo = $this->getDoctrine()->getrepository('AppBundle:AccountRequest');
             $em = $this->getDoctrine()->getManager();
             $em->persist($account);
             $em->flush();

             // TODO pagina ringraziamento?
             return $this->redirectToRoute('home');
    	}

        return $this->render('editUser.html.twig', [
            'form' => $form->createView(),
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

}
