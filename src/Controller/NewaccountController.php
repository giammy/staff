<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Staff;
use App\Entity\Account;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\IsTrue;

use Psr\Log\LoggerInterface;

class NewaccountController extends AbstractController
{

    private $params;
    public function __construct(ParameterBagInterface $params) {
        $this->params = $params;
    }

    /**
     * @Route("/newaccount", name="newaccount")
     */
    public function newaccountIndex(Request $request, \Swift_Mailer $mailer,
    	   	    	            LoggerInterface $appLogger)
    {
        $account = new Account();
        $account->setRequested(new \DateTime(date('Y-m-d H:i:s')));

        $form = $this->createFormBuilder($account)
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('contactPerson', TextType::class)
            ->add('accountIsNew', ChoiceType::class, array(
				 'expanded' => true,
				 'multiple' => false,
				 'choices'  => array(
					 'Yes' => true,  
		    	      	         'No' => false,),
		                  'data' => true,
		  	         ))

            ->add('validFrom', DateType::class, array('data' => new \DateTime()))
            ->add('validTo', DateType::class, array('data' => new \DateTime()))
            ->add('profile', ChoiceType::class, array(
				'choices'  => array(
				'CNR' => 'CNR',
				'UNIPD' => 'UNIPD',
				'Consorzio RFX' => 'Consorzio RFX',
				'ENEA' => 'ENEA',
				'Dottorando' => 'Dottorando',
				'Tesista' => 'Tesista',
				'Ospite' => 'Ospite',
				'Consulente' => 'Consulente',
			      	'Borsista' => 'Borsista',
			    	'Perfezionando' => 'Perfezionando',
				'Altro' => 'Altro',
			),
		))
            ->add('groupName', ChoiceType::class, array(
			 'choices'  => array(
			    'AI' => 'GAI',
			    'FA' => 'GFA',
			    'FB' => 'GFB',
	    	            'FC' => 'GFC',
			    'FD' => 'GFD',
			    'FT' => 'GFT',
			    'Operation of Facilities' => 'GOP',
		      	    'IP' => 'GIP',
		    	    'SE' => 'GSE',
			    'IE' => 'GIE',
			    'IT' => 'IT',
 	       	            'Officine' => 'OME',
       		            'SX-Alimentazioni' => 'SXA',
		            'SX-Controlli' => 'SXC',
     		            'SX-Diagnostiche' => 'SXD',
   		       	    'SX-Macchina' => 'SXM',
		      	    'Ufficio Acquisti' => 'AMM',
     			    'Ufficio Manutenzione' => 'SMA',
	  		    'Ufficio Tecnico' => 'UTE',
      			    'Ospiti' => 'Ospiti',
			    'Altro' => 'Altro',
	  	           ),
	          ))

            ->add('emailEnabled', ChoiceType::class, array(
                                 'expanded' => true,
                                 'multiple' => false,
                                 'choices'  => array(
                                            'Yes' => true,
                                            'No' => false,
                                            ),
                                 'data' => false,
                                           ))
            ->add('windowsEnabled', ChoiceType::class, array(
                                 'expanded' => true,
                                 'multiple' => false,
                                 'choices'  => array(
                                            'Yes' => true,
                                            'No' => false,
                                            ),
                                 'data' => false,
                                           ))
            ->add('linuxEnabled', ChoiceType::class, array(
                                 'expanded' => true,
                                 'multiple' => false,
                                 'choices'  => array(
                                            'Yes' => true,
                                            'No' => false,
                                            ),
                                 'data' => false,
                                           ))
            ->add('itRegulationAccepted', CheckboxType::class, array(
                                 'mapped' => false,
                                 'constraints' => [
                                    new IsTrue([
                                       'message' => 'I know, it\'s silly, but you must agree to our terms.'
                                   ])],
                                 //'expanded' => false,
                                 //'multiple' => false,
                                 //'choices'  => array(
                                 //           'Yes' => true,
                                 //           ),
                                 //'data' => false,
                                           ))
            ->add('note', TextType::class, array(
  	          	      'required'    => false,
	      		        // 'placeholder' => '',
			       'empty_data'  => null
	       		         ))
            //->add('save', SubmitType::class, array('label' => 'SEND REQUEST'))
            ->getForm();

	$form->handleRequest($request);

        $appLogger->info("IN: newaccountIndex: username='" .
            $this->get('security.token_storage')->getToken()->getUser()->getUsername() .
            "' isSubmitted=" . ($form->isSubmitted()?"TRUE":"FALSE") . 
            " isValid=" . ($form->isSubmitted()?($form->isValid()?"TRUE":"FALSE"):"---") .
            " form surname,name='" . $account->getSurname() . "," . $account->getName() . "'"
            );

	if ($form->isSubmitted() && $form->isValid()) {
             // $form->getData() holds the submitted values
             // but, the original `$task` variable has also been updated
             $username = $this->get('security.token_storage')->getToken()->getUser()->getUsername();
	     $account = $form->getData();
	     $account->setUsername($username);
             $account->setVersion($this->params->get('account_current_db_format_version'));
             //$account->setValidFrom(new \DateTime(date('Y-m-d H:i:s')));
             //$account->setValidTo(new \DateTime(date('Y-m-d H:i:s')));
             $account->setItRegulationAccepted(true); // form input will force the check of this flag

	     // save
	     //$repo = $this->getDoctrine()->getrepository('AppBundle:AccountRequest');
             $em = $this->getDoctrine()->getManager();
             $em->persist($account);
             $em->flush();

	     $mailsToNotify = preg_split('/, */', $this->params->get('newaccount_mailstonotify'));
	     array_push($mailsToNotify, $username . '@igi.cnr.it');

             // send emails
             foreach ($mailsToNotify as $recipient) {
		 $message = (new \Swift_Message('New account request from ' . $username . ' for ' . 
                                  $account->getName() . ' ' . $account->getSurname()))
		     ->setFrom('webmaster@igi.cnr.it')
		     ->setTo($recipient)
		     ->setBody(
			$this->renderView(
                         'newaccount/requestEmail.html.twig',
                          array('ar' => $account)
                        ),'text/html');
		 $mailer->send($message);
             }
            return $this->redirectToRoute('thanks');
    	}

        return $this->render('newaccount/index.html.twig', [
            'form' => $form->createView(),
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/newaccount/showall", name="newaccountshowall")
     */
    public function newaccountShowallIndex(LoggerInterface $appLogger)
    {
        $username = $this->get('security.token_storage')->getToken()->getUser()->getUsername();
	$allowedUsers = preg_split('/, */', $this->params->get('users_newaccount_admin'));
        if (in_array($username, $allowedUsers)) {
            $appLogger->info("IN: newaccountShowallIndex: username='" . $username . "' allowed");
            $repo = $this->getDoctrine()->getRepository(Account::class);
	    return $this->render('newaccount/showall.html.twig', [
                'controller_name' => 'NewaccountShowallController',
                'list' => $repo->findAll(),
                ]);
        } else {
            $appLogger->info("IN: newaccountShowallIndex: username='" . $username . "' NOT allowed");
            return $this->redirectToRoute('newaccount');
        }
    }

    /**
     * @Route("newaccount/thanks", name="thanks")
     */
    public function thanksAction(Request $request)
    {
        return $this->render('newaccount/thanks.html.twig', array(
            'note' => "note",
            ));
    }

}
