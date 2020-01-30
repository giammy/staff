<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Log\LoggerInterface;
use App\Entity\Staff;
use App\Entity\Account;
use App\Services\ConvertSurnameNameToUsernameData;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use App\Services\ExportPersonaleService;

class RootController extends AbstractController
{
    private $params;
    private $exportPersonaleService;
    private $convertSurnameNameToUsernameData;

    public function __construct(ParameterBagInterface $params,
                                ConvertSurnameNameToUsernameData $convertSurnameNameToUsernameData,
                                ExportPersonaleService $exportPersonaleService) {
        $this->params = $params;
        $this->exportPersonaleService = $exportPersonaleService;
        $this->convertSurnameNameToUsernameData = $convertSurnameNameToUsernameData;
    }

    public function internalDoLog(LoggerInterface $appLogger, string $action, string $where, $account) {
        $appLogger->info($action . ": " . $where . ": data: " . 
              $account->getId() . "," . 
              $account->getUsername() . "," . 
              $account->getEmail() . "," . 
              $account->getSecondaryEmail() . ",'" . 
              $account->getName() . "','" . 
              $account->getSurname() . "'," . 
              $account->getGroupName() . "," . 
              $account->getLeaderOfGroup() . "," . 
              $account->getQualification() . "," . 
              $account->getOrganization() . "," . 
              $account->getTotalHoursPerYear() . "," . 
              $account->getTotalContractualHoursPerYear() . "," . 
              $account->getParttimePercent() . "," . 
              $account->getIsTimeSheetEnabled() . "," . 
              $account->getCreated()->format($this->params->get('date_format')) . "," . 
              $account->getValidFrom()->format($this->params->get('date_format')) . "," . 
              $account->getValidTo()->format($this->params->get('date_format')) . "," . 
              $account->getVersion() . ",'" . 
              $account->getNote() . "','" . 
              $account->getOfficePhone() . "','" . 
              $account->getOfficeMobile() . "','" . 
              $account->getOfficeLocation() . "','" . 
              $account->getInternalNote() . "'," . 
              $account->getLastChangeAuthor() . "," . 
              $account->getLastChangeDate()->format($this->params->get('date_format'))
              );
    }

    public function internalCheckUsername(LoggerInterface $appLogger, $account) {
	if ($account->getUsername() != null) {
             return;
        }
        $appLogger->info("IN internalCheckUsername: missing username!");
	$usernameData = $this->convertSurnameNameToUsernameData->convert(strtoupper($account->getSurname() . " " . $account->getName()), $account->getSurname(), $account->getName());
        //echo("<pre>"); var_dump($usernameData); exit;
	if ((strtoupper($usernameData['surname']) == strtoupper($account->getSurname())) && 
            (strtoupper($usernameData['name']) == strtoupper($account->getName())) )  {
            $appLogger->info("IN internalCheckUsername: AUTOFILL:UE, found username " . $usernameData['username'] . " for user '" . $account->getSurname() . "' '" . $account->getName() . "'");
	    $account->setUsername($usernameData['username']);
	    $account->setEmail($usernameData['email']);
            $account->setInternalNote("AUTOFILL:UE," . $account->getInternalNote());
        }
    }

    public function internalCheckValidityDates(LoggerInterface $appLogger, $account) {
        $appLogger->info("IN internalCheckValidityDates: TODO? should change something?");
    }


    /**
     * @Route("/", name="home")
     */
    public function homeAction(LoggerInterface $appLogger)
    {
        $username = $this->get('security.token_storage')->getToken()->getUser()->getUsername();
        $appLogger->info("IN: homeAction: username='" . $username . "'");

        return $this->render('index.html.twig', [
            'controller_name' => 'HomeController',
            'username' => $username,
        ]);
    }

    /**
     * @Route("/showall/{which}/{mode}", name="showall")
     */
    public function showallAction(LoggerInterface $appLogger, $which="active", $mode="html")
    {
        $username = $this->get('security.token_storage')->getToken()->getUser()->getUsername();
	$allowedUsers = preg_split('/, */', $this->params->get('users_ufficio_personale'));
        if (!in_array($username, $allowedUsers)) {
            $appLogger->info("IN: showallAction: username='" . $username . "' NOT allowed");
            return $this->redirectToRoute('home');
        }

        $appLogger->info("IN: showallAction: username='" . $username . "' allowed");
        $repo = $this->getDoctrine()->getRepository(Staff::class);
        $dateNow = new \DateTime();
        // $listToShow = $repo->findAll();
        $listToShow = $repo->findBy([], ['surname' => 'ASC', 'lastChangeDate' => 'DESC']);
        // listToShow is sorted by surname

        if ($which == "active") {
            // show active records
            $listToShow = array_values(array_filter($listToShow, function ($x) use ($dateNow) { 
                    // return (($x->getValidFrom() <= $dateNow) && ($dateNow <= $x->getValidTo())); 
                    return ($dateNow <= $x->getValidTo()); 
            }));
        } else if ($which == "auto") {
            // show only autofill
            $listToShow = array_values(array_filter($listToShow, function ($x) use ($dateNow) { 
                    return ((strpos($x->getInternalNote(), 'AUTOFILL') !== false)); 
            }));
        }
       
        if ($mode == "csv") {
           //$resp = new Response();
           $resp = $this->render('showall.csv.twig', [
               'controller_name' => 'ShowallController',
               'list' => $listToShow,
               'username' => $this->get('security.token_storage')->getToken()->getUser()->getUsername(),
     	       ]);
           $resp->headers->set('Content-Type', 'text/csv');
           $resp->headers->set('Content-Disposition', 
                               'attachment; filename="staff_export_' . $dateNow->format('YmdHis\U\TC') . '.csv"');
        } else {
            $resp =  $this->render('showall.html.twig', [
                'controller_name' => 'ShowallController',
                'list' => $listToShow,
                'username' => $this->get('security.token_storage')->getToken()->getUser()->getUsername(),
            ]);
        }

        return $resp;
    }

    /**
     * @Route("/deleteUser/{id}", name="deleteUser")
     */
    public function deleteUserAction(Request $request, \Swift_Mailer $mailer, LoggerInterface $appLogger, $id="-1")
    {
        $id = intval($id);
        $appLogger->info("IN: deleteUserAction id=" . $id);

        $username = $this->get('security.token_storage')->getToken()->getUser()->getUsername();
	$allowedUsers = preg_split('/, */', $this->params->get('users_ufficio_personale'));
        if (!in_array($username, $allowedUsers)) {
            $appLogger->info("IN: deleteUserAction: username='" . $username . "' NOT allowed");
            return $this->redirectToRoute('home');
        }

        $repo = $this->getDoctrine()->getRepository(Staff::class);
	$account = $repo->find($id);
        if ($account) {
            $this->internalDoLog($appLogger, "DELETE", "IN deleteUserAction: username='" .
                $this->get('security.token_storage')->getToken()->getUser()->getUsername() . "'", $account);
            $em = $this->getDoctrine()->getManager();
            $em->remove($account);
            $em->flush();
        }
        return $this->redirectToRoute('showall');
    }


    /**
     * @Route("/confirmUserAutofill/{id}", name="confirmUserAutofill")
     */
    public function confirmUserAutofillAction(Request $request, \Swift_Mailer $mailer, LoggerInterface $appLogger, $id="-1")
    {
        $id = intval($id);
        $appLogger->info("IN: confirmUserAutofillAction id=" . $id);

        $username = $this->get('security.token_storage')->getToken()->getUser()->getUsername();
	$allowedUsers = preg_split('/, */', $this->params->get('users_it'));
        if (!in_array($username, $allowedUsers)) {
            $appLogger->info("IN: confirmUserAutofillAction: username='" . $username . "' NOT allowed");
            return $this->redirectToRoute('home');
        }

        $repo = $this->getDoctrine()->getRepository(Staff::class);
	$account = $repo->find($id);
        if ($account) {
            $this->internalDoLog($appLogger, "CONFIRM", "IN confirmUserAutofillAction: username='" .
                $this->get('security.token_storage')->getToken()->getUser()->getUsername() . "'", $account);
            $em = $this->getDoctrine()->getManager();
            $account->setInternalNote(str_replace("AUTOFILL:UE,", "", $account->getInternalNote()));
            $em->persist($account);
            $em->flush();
        }
        return $this->redirectToRoute('showall', array('which' => 'html', 'mode' => 'auto'));
    }

    /**
     * @Route("/editUser/{id}", name="editUser")
     */
    public function editUserAction(Request $request, \Swift_Mailer $mailer, LoggerInterface $appLogger, $id="-1")
    {
        //$appLogger->info("IN: editUserAction");
        $id = intval($id);
        $repo = $this->getDoctrine()->getRepository(Staff::class);

        $username = $this->get('security.token_storage')->getToken()->getUser()->getUsername();
	$allowedUsers = preg_split('/, */', $this->params->get('users_ufficio_personale'));
        if (!in_array($username, $allowedUsers)) {
            $appLogger->info("IN: editUserAction: username='" . $username . "' NOT allowed");
            return $this->redirectToRoute('home');
        }

        // if id == -1 -> new user, else edit id user TODO
	$account = $repo->find($id);
        $oldAccount = $account;
        if (!$account) {
            // id does not exist: create new user
            $account = new Staff();
	    $dt = new \DateTime(date('Y-m-d H:i:s'));
            $account->setCreated($dt);
            $account->setValidFrom($dt);
            $account->setValidTo(new \DateTime('2099-12-31 11:59:59'));
            $account->setTotalContractualHoursPerYear(1720);
            $account->setParttimePercent(100);
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
            ->add('parttimePercent', NumberType::class, array(
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
            ->add('validFrom', DateType::class, array('data' => $account->getValidFrom()))
            ->add('validTo', DateType::class, array(
                                  'years' => range(1980, 2099),
                                  'data' => $account->getValidTo(),

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

        $theClass = "button";
        $theStyle = "width:256px;";
        $form->add('newrecord', SubmitType::class, array('label' => 'NUOVA POSIZIONE',
                'attr' => array('class' => $theClass, 'style' => $theStyle )
            ));
        $form->add('update', SubmitType::class, array('label' => 'AGGIORNA',
                'attr' => array('class' => $theClass, 'style' => $theStyle )
            ));
	$form->handleRequest($request);

        $appLogger->info("IN: editUserAction: username='" .
            $this->get('security.token_storage')->getToken()->getUser()->getUsername() .
            "' isSubmitted=" . ($form->isSubmitted()?"TRUE":"FALSE") . 
            " isValid=" . ($form->isSubmitted()?($form->isValid()?"TRUE":"FALSE"):"---") .
            " form username='" . $account->getUsername() . "' (" . 
            $account->getSurname() . ", " . $account->getName() . ")"
            );

	if ($form->isSubmitted() && $form->isValid()) {
             // $form->getData() holds the submitted values
             // but, the original variable has also been updated

             $pressedButtonName = $form->getClickedButton()->getName();
             // echo("<pre>");var_dump($pressedButtonName); exit;
             $newRecordRequest = ($pressedButtonName == 'newrecord');
             //var_dump($newRecordRequest); exit;

	     $account = $form->getData();
             $account->setTotalHoursPerYear(($account->getTotalContractualHoursPerYear()*
                                             $account->getParttimePercent())/100);
	     $account->setLastChangeAuthor($this->get('security.token_storage')->getToken()->getUser()->getUsername());
	     $account->setLastChangeDate(new \Datetime());

             $em = $this->getDoctrine()->getManager();

             if ($oldAccount == null) { // new entry
                 $account->setVersion($this->params->get('staff_current_db_format_version'));
		 $this->internalCheckUsername($appLogger, $account);
                 $em->persist($account);
                 $this->internalDoLog($appLogger, "NEWENTRY", "IN editUserAction: username='" .
                     $this->get('security.token_storage')->getToken()->getUser()->getUsername()."'", $account);
             } else if ($newRecordRequest) {
                 // change validity dates? duplicate? store new version?
                 $newAccount = new Staff();
                 $newAccount = clone $account;
                 $em->detach($account);
		 $this->internalCheckUsername($appLogger, $newAccount);
                 $em->persist($newAccount);
                 $this->internalCheckValidityDates($appLogger, $newAccount);
                 $this->internalDoLog($appLogger, "NEWRECORD", "IN editUserAction: username='" .
                     $this->get('security.token_storage')->getToken()->getUser()->getUsername()."'", $newAccount);
             } else {
                 // just update
		 $this->internalCheckUsername($appLogger, $account);
                 $this->internalDoLog($appLogger, "UPDATE", "IN editUserAction: username='" .
                     $this->get('security.token_storage')->getToken()->getUser()->getUsername()."'", $account);
                 $em->persist($account);
             }
             $em->flush();

	     // use default filename from environment variable EXPORT_PERSONALE_FILENAME
             $this->exportPersonaleService->export(null); 

             // TODO pagina ringraziamento?
             return $this->redirectToRoute('showall');
    	}

        return $this->render('editUser.html.twig', [
            'form' => $form->createView(),
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

}
