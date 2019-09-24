<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Staff;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class NewaccountController extends AbstractController
{
    /**
     * @Route("/newaccount", name="newaccount")
     */
    public function newaccountIndex(Request $request)
    {
        $staff = new Staff();
        $staff->setCreated(new \DateTime(date('Y-m-d H:i:s')));

        $form = $this->createFormBuilder($staff)
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('accountContactPerson', TextType::class)
            ->add('accountIsNew', ChoiceType::class, array(
				 'expanded' => true,
				 'multiple' => false,
				 'choices'  => array(
					 'Yes' => true,  
		    	      	         'No' => false,),
		                  'data' => false,
		  	         ))

            ->add('accountStartDate', DateType::class, array('data' => new \DateTime()))
            ->add('accountEndDate', DateType::class, array('data' => new \DateTime()))
            ->add('accountProfile', ChoiceType::class, array(
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
		      	    'IP' => 'GIP',
		    	    'SE' => 'GSE',
			    'IE' => 'GIE',
                            'NBI' => 'NBI',
                            'DIR' => 'DIR',
			    'IT' => 'IT',
 	       	            'Officine' => 'OME',
       		            'SX-Alimentazioni' => 'SXA',
		            'SX-Controlli' => 'SXC',
     		            'SX-Diagnostiche' => 'SXD',
   		       	    'SX-Macchina' => 'SXM',
		      	    'Amministrazione' => 'AMM',
     			    'Ufficio Manutenzione' => 'SMA',
	  		    'Ufficio Tecnico' => 'UTE',
      			    'Ospiti' => 'Ospiti',
			    'Altro' => 'Altro',
	  	           ),
	          ))

            ->add('accountEmailEnabled', ChoiceType::class, array(
                                 'expanded' => true,
                                 'multiple' => false,
                                 'choices'  => array(
                                            'Yes' => false,  // label says "IS NEW", so invert
                                            'No' => true,
                                            ),
                                 'data' => false,
                                           ))
            ->add('accountWindowsEnabled', ChoiceType::class, array(
                                 'expanded' => true,
                                 'multiple' => false,
                                 'choices'  => array(
                                            'Yes' => false,  // label says "IS NEW", so invert
                                            'No' => true,
                                            ),
                                 'data' => false,
                                           ))
            ->add('accountLinuxEnabled', ChoiceType::class, array(
                                 'expanded' => true,
                                 'multiple' => false,
                                 'choices'  => array(
                                            'Yes' => false,  // label says "IS NEW", so invert
                                            'No' => true,
                                            ),
                                 'data' => false,
                                           ))
            ->add('note', TextType::class, array(
  	          	      'required'    => false,
	      		        // 'placeholder' => '',
			       'empty_data'  => null
	       		         ))
            //->add('save', SubmitType::class, array('label' => 'SEND REQUEST'))
            ->getForm();

	$form->handleRequest($request);

	if ($form->isSubmitted() && $form->isValid()) {
             // $form->getData() holds the submitted values
             // but, the original `$task` variable has also been updated
	     $staff = $form->getData();
             $staff->setUsername("");
             $staff->setEmail("");
             $staff->setQualification("");
             $staff->setOrganization("");
             $staff->setTotalHoursPerYear(0);
             $staff->setTotalContractualHoursPerYear(0);
             $staff->setParttimePercent(0);
             $staff->setIsTimesheetEnabled(false);
             $staff->setValidFrom(new \DateTime(date('Y-m-d H:i:s')));
             $staff->setValidTo(new \DateTime(date('Y-m-d H:i:s')));
             $staff->setVersion(1);
	     $staff->setAccountRequestDone(false);
	     $staff->setAccountSipraDone(false);

	     // save
	     //$repo = $this->getDoctrine()->getrepository('AppBundle:AccountRequest');
             $em = $this->getDoctrine()->getManager();
             $em->persist($staff);
             $em->flush();

/*
	     // Using service to get LDAP attributes
             $srvc = $this->container->get('get.attributes');
	     $attributes = $srvc->getAttributes($this->getUser()->getUsername());

	     $mailsToNotify = $this->container->getParameter('mailsToNotify');
		     array_push($mailsToNotify, $attributes['email']);

             // OK ALL DONE!
	          foreach ($mailsToNotify as $recipient) {
		           $message = \Swift_Message::newInstance()
			                ->setSubject('New account request from ' . $ar->getGroupHead() . ' for ' . $ar->getName() . ' ' . $ar->getSurname())
							       ->setFrom('webmaster@igi.cnr.it')
								     ->setTo($recipient)
									     ->setBody(
											$this->renderView(
                        'emails/newaccount.html.twig',
                        array('ar' => $ar)
                      ),'text/html');
		                $this->get('mailer')->send($message);
				     }
*/
            return $this->redirectToRoute('newaccount');
    	}

        // replace this example code with whatever you need
        return $this->render('newaccount/index.html.twig', [
            'form' => $form->createView(),
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);



//        return $this->render('newaccount/index.html.twig', [
//            'controller_name' => 'NewaccountController',
//        ]);
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
