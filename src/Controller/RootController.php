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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
	if ($account->getUsername() != null &&
            $account->getEmail() != null &&
            $account->getOfficePhone() != null) {
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
	    $account->setOfficePhone($usernameData['telephonenumber']);
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


/// START DB REFACTOR
/*
    	foreach ($listToShow as $ll) {
            	 if ($ll->getGroupName() == 'SCA') $ll->setGroupName('SIT');
             	 if ($ll->getGroupName() == 'GFA') $ll->setGroupName('GFO');
            	 if ($ll->getGroupName() == 'GFC') $ll->setGroupName('GFS');
            	 if ($ll->getGroupName() == 'OME') $ll->setGroupName('UTE');
            	 if ($ll->getLeaderOfGroup() == 'SCA') $ll->setLeaderOfGroup('SIT');
             	 if ($ll->getLeaderOfGroup() == 'GFA') $ll->setLeaderOfGroup('GFO');
            	 if ($ll->getLeaderOfGroup() == 'GFC') $ll->setLeaderOfGroup('GFS');
            	 if ($ll->getLeaderOfGroup() == 'OME') $ll->setLeaderOfGroup('UTE');
           $em = $this->getDoctrine()->getManager();
           $em->flush();
}
*/
///


        if ($which == "active") {
            // show active records
            $listToShow = array_values(array_filter($listToShow, function ($x) use ($dateNow) { 
                    // return (($x->getValidFrom() <= $dateNow) && ($dateNow <= $x->getValidTo())); 
                    return ($dateNow->format('Y') <= $x->getValidTo()->format('Y'));
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

// curl -u "rfx:password" "https://www.pd.cnr.it/services/rfx-api/rubrica/select.php?name=Gianluca&surname=Moro"
// [{"id":"60092","name":"Gianluca","surname":"Moro","phone":"5095","phone2":"","fax":""}]

    public function exportToAreaCards($appLogger, $account) {
         $appLogger->info("CARDS: exportToAreaCards: username='" .
                     $this->get('security.token_storage')->getToken()->getUser()->getUsername()."' - " .
		     		$account->getUsername() . " " .
		     		$account->getSurname() . " " .
		     		$account->getName() . " " .
				$account->getOfficePhone() . " " .
				$account->getOfficeMobile()  );

	// build command line
	$name = urlencode($account->getName());
	$surname = urlencode($account->getSurname());
	$phone = strtok($account->getOfficePhone(),",");
	$phone2 = strtok($account->getOfficeMobile(), ",");

	if ($phone == "0000" or $phone == "") {
            $appLogger->info("CARDS: exportToAreaCards: SKIP - no phone");
	    return;
        }

	$baseUrl = "https://www.pd.cnr.it/services/rfx-api/rubrica/update.php?";
	$params = "name=" . $name . "&surname=" . $surname . "&phone=" . $phone;
	if ($phone2 != "0000" and $phone2 != "") {
	   $params = $params . "&phone2=" . $phone2;
           $appLogger->info("CARDS: exportToAreaCards: URLPHONE2=...." . $params);
        }
	$url = $baseUrl . $params;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD, "rfx:rfx_CNR3v1x");
        $result = curl_exec($ch);
        curl_close($ch);
	$appLogger->info("CARDS: exportToAreaCards: URL=" . $url . " JSONRES=" . $result);

}


    /**
     * @Route("/editUser/{id}", name="editUser")
     */
    public function editUserAction(Request $request, \Swift_Mailer $mailer, LoggerInterface $appLogger, $id="-1")
    {
        //$appLogger->info("IN: editUserAction");
        $id = intval($id);
        $repo = $this->getDoctrine()->getRepository(Staff::class);

	$localFilesDirectoryPrefix = $this->params->get('local_files_directory');

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

        $descriptionList = $account->getDescriptionList();
	if (is_array($descriptionList)) {
	    $dl1 = (count($descriptionList)>0)?$descriptionList[0][0]:'';
            $dd1 = (count($descriptionList)>0)?$descriptionList[0][1]:'';
            $dl2 = (count($descriptionList)>1)?$descriptionList[1][0]:'';
            $dd2 = (count($descriptionList)>1)?$descriptionList[1][1]:'';
        } else {
            $appLogger->info("IN editUserAction: descriptionList is not an array - account username=" . $account->getUsername());
            $dl1 = ''; $dd1 = ''; $dl2 = ''; $dd2 = '';
        }
        if (strlen($dl1) == 0) { $dl1 = 'Short description'; }
        if (strlen($dl2) == 0) { $dl2 = 'Activity'; }

        $attachList = $account->getAttachList();
	if (is_array($attachList)) {
            $photoWebFilename = (count($attachList)>0)?$attachList[0][1]:'';
        } else {
	    $photoWebFilename = '';
	}
        if (!file_exists($localFilesDirectoryPrefix . "/" . $photoWebFilename)) {
            $appLogger->info("IN: editUserAction: photoWebFilename='" . $photoWebFilename . "' DOES NOT EXIST!");
            $photoWebFilename = '';
        }

/*
	$theOldChoices = array(
			    'AI' => 'GAI',
			    'FA' => 'GFA',
			    'FB' => 'GFB',
	    	            'FC' => 'GFC',
			    'FD' => 'GFD',
			    'FT' => 'GFT',
			    'Operation of Facilities' => 'GOP',
		      	    'IP' => 'GIP',
			    'IS' => 'GIS',
			    'IM' => 'GIM',
		    	    'SE' => 'GSE',
			    'IE' => 'GIE',
			    'SIT' => 'SCA',
			    'NBI' => 'NBI',
 	       	            'Officine' => 'OME',
       		            'SX-Alimentazioni' => 'SXA',
		            'SX-Controlli' => 'SXC',
     		            'SX-Diagnostiche' => 'SXD',
   		       	    'SX-Macchina' => 'SXM',
		      	    'Direzione' => 'DIR',
		      	    'Amministrazione' => 'AMM',
     			    'SMA - Maintenance' => 'SMA',
	  		    'Ufficio Tecnico' => 'UTE',
    	  		    'Officina Meccanica' => 'OME',
			    'Altro' => 'BLK',
	  	         );
*/
/*
	$theSecondOldChoices = [
	    'Research' => [
	        'GAI Automation Engineering and Information Technology' => 'GAI',
	        'GFA FA - Physics' => 'GFA',
	        'GFB FB - Physics' => 'GFB',
	        'GFC FC - Physics' => 'GFC',
	        'GFD FD - Physics' => 'GFD',
	        'GFT Theoretical Physics' => 'GFT',
	        'GIE Electric and Magnetic Fields Engineering' => 'GIE',
	        'GIP Thermomechanics, Vacuum and Plasma Engineering' => 'GIP',
	        'GIS Thermo-mechanical systems engineering' => 'GIS',
	        'GIM Thermo-mechanical components engineering' => 'GIM',
	        'GSE Power Systems Engineering' => 'GSE',
	        'NBI NBTF Organization' => 'NBI',
	        'GOP Operation of Facilities' => 'GOP',
	    ],
	    'Technical Staff' => [
		'OME Mechanics Workshop' => 'OME',
	        'SIT Information Technology' => 'SCA',
	        'SMA Maintenance' => 'SMA',
	        'SXA Power Supply' => 'SXA',
	        'SXC Controls and Data Acquisition' => 'SXC',
	        'SXD Diagnostics' => 'SXD',
	        'SXM Machine' => 'SXM',
	        'UTE Drawing office' => 'UTE',
	        'OME Mechanical off.' => 'OME',
	    ],
	    'Administration' => [
	       'AAP Administration & Purchasing' => 'AAP',
	       'APG Personnel' => 'APG',
	    ],
	    'Direction' => [
               'DIR Direction' => 'DIR',
	       'SAD Programme and Strategy Group' => 'SAD',
	       'GCP Scientific and Technological Programmes' => 'GCP',
	       'GCL Group of Collaborators' => 'GCL',
	       'SPP Prevention and Protection' => 'SPP',
	       'QMA Quality Management & GDPR' => 'QMA', 
	       'FPL Financial Planning' => 'FPL',
	       'HET Higher Education and Training' => 'HET',
	       'ERC Communication and External Relations' => 'ERC',
	       'RSL Scientific Secretary Library' => 'RSL',
	       'ADR Chairperson and Director Staff' => 'ADR',
            ],
	];
*/

	$theNewChoices = [
	    'Research' => [
	        'GAI Automation Engineering and Information Technology' => 'GAI',
	        'GFO FO - Physics' => 'GFO',
	        'GFB FB - Physics' => 'GFB',
	        'GFS FS - Physics' => 'GFS',
//	        '' => 'GFD',
	        'GFT Theoretical Physics' => 'GFT',
	        'GIE Electric and Magnetic Fields Engineering' => 'GIE',
//	        'GIP Thermomechanics, Vacuum and Plasma Engineering' => 'GIP',
	        'GIS Thermo-mechanical systems engineering' => 'GIS',
	        'GIM Thermo-mechanical components engineering' => 'GIM',
                'GSE Power Systems Engineering' => 'GSE',
	        'NBI NBTF Organization' => 'NBI',
	        'GOP Operation of Facilities' => 'GOP',
	    ],
	    'Technical Staff' => [
	        'SIT Information Technology' => 'SIT',
	        'SMA Maintenance' => 'SMA',
	        'SXA Power Supply' => 'SXA',
	        'SXC Controls and Data Acquisition' => 'SXC',
	        'SXD Diagnostics' => 'SXD',
	        'SXM Machine' => 'SXM',
	        'UTE Drawing Office' => 'UTE',
		'OME Mechanical off.' => 'OME',
	    ],
	    'Administration' => [
	       'AAP Administration & Purchasing' => 'AAP',
	       'APG Personnel' => 'APG',
	    ],
	    'Direction' => [
               'DIR Direction' => 'DIR',
	       'SAD Programme and Strategy Group' => 'SAD',
	       'GCP Scientific and Technological Programmes' => 'GCP',
	       'GCL Group of Collaborators' => 'GCL',
	       'SPP Prevention and Protection' => 'SPP',
	       'QMA Quality Management & GDPR' => 'QMA', 
	       'FPL Financial Planning' => 'FPL',
	       'HET Higher Education and Training' => 'HET',
	       'ERC Communication and External Relations' => 'ERC',
	       'RSL Scientific Secretary Library' => 'RSL',
	       'ADR Chairperson and Director Staff' => 'ADR',
            ],
	];
	
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
			 'choices'  => $theNewChoices,
	          ))
            ->add('leaderOfGroup', ChoiceType::class, array(
                         'required' => false,
                         'placeholder' => 'Se capogruppo, indicare il gruppo',
 			 'multiple' => true,
			 'mapped' => false,
			 'expanded' => true,
			 'data' => explode(":", $account->getLeaderOfGroup()),
			 'choices'  => $theNewChoices,
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
    	    	            'BLK' => 'BLK',
    	    	            'EXT' => 'EXT',
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
            ->add('note', TextareaType::class, array(
                         'required' => false,))
            ->add('officePhone', TextType::class, array(
                         'required' => false,))
            ->add('officeMobile', TextType::class, array(
                         'required' => false,))
            ->add('officeLocation', TextType::class, array(
                        'required' => false,))
            ->add('descriptionL1', TextType::class, array('data' => $dl1, 'required' => false, 'mapped' => false))
            ->add('descriptionD1', TextType::class, array('data' => $dd1, 'required' => false, 'mapped' => false))
            ->add('descriptionL2', TextType::class, array('data' => $dl2, 'required' => false, 'mapped' => false))
            ->add('descriptionD2', TextareaType::class, array('data' => $dd2, 'required' => false, 'mapped' => false, ))
	    ->add('photoWeb', FileType::class, array('data' => null, 'required' => false, 'mapped' => false, ))

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

	     $account->setLeaderOfGroup(implode(": ", $form->get('leaderOfGroup')->getData()));

             $account->setTotalHoursPerYear(($account->getTotalContractualHoursPerYear()*
                                             $account->getParttimePercent())/100);
	     $account->setLastChangeAuthor($this->get('security.token_storage')->getToken()->getUser()->getUsername());
	     $account->setLastChangeDate(new \Datetime());

             // manage descriptions' extra fields
             $dl1 = $form->get('descriptionL1')->getData();
             $dd1 = $form->get('descriptionD1')->getData();
             $dl2 = $form->get('descriptionL2')->getData();
             $dd2 = $form->get('descriptionD2')->getData();
             if (strlen($dl1) == 0) { $dl1 = 'Short description'; }
             if (strlen($dl2) == 0) { $dl2 = 'Activity'; }
             $account->setDescriptionList([[$dl1,$dd1],[$dl2,$dd2]]);

             // manage attached files
             $theUploadedFile = $form->get('photoWeb')->getData();
             if ($theUploadedFile) {
               $theName = (new \DateTime())->format($this->params->get('date_format_for_filename')) . "-" .
                 preg_replace("/[^A-Za-z0-9-_.]/", "", $theUploadedFile->getClientOriginalName());
               //echo("<pre>");var_dump($theName); var_dump($theUploadedFile); exit;
               $theUploadedFile->move($localFilesDirectoryPrefix, $theName);
               $account->setAttachList([['photoWeb', $theName]]);
             }

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

	     $this->exportToAreaCards($appLogger, $account);

	     // use default filename from environment variable EXPORT_PERSONALE_FILENAME
             $this->exportPersonaleService->export(null); 

             // TODO pagina ringraziamento?
             return $this->redirectToRoute('showall');
    	}

        $root_dir = realpath($this->getParameter('kernel.root_dir').'/..');
        if (strpos($root_dir, 'www/html/staff') !== false) {
            $baseUrl = "/staff/";
        } else {
            $baseUrl = "/";
        }

        return $this->render('editUser.html.twig', [
            'id' => $id,
            'username' => $username,
            'baseUrl' => $baseUrl,
            'photoWeb' => $photoWebFilename,
            'form' => $form->createView(),
            'base_dir' => $root_dir.DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/deletePhoto/{id}", name="deletePhoto")
     */
    public function deletePhotoAction(Request $request, \Swift_Mailer $mailer, LoggerInterface $appLogger, $id="-1")
    {
        $id = intval($id);
        $repo = $this->getDoctrine()->getRepository(Staff::class);

        $localFilesDirectoryPrefix = $this->params->get('local_files_directory');

        $username = $this->get('security.token_storage')->getToken()->getUser()->getUsername();
	$allowedUsers = preg_split('/, */', $this->params->get('users_ufficio_personale'));
        if (!in_array($username, $allowedUsers)) {
            $appLogger->info("IN: editUserAction: username='" . $username . "' NOT allowed");
            return $this->redirectToRoute('home');
        }

        // if id == -1 -> new user, else edit id user TODO
	$account = $repo->find($id);
        if ($account) {
            // delete photo on existing record :-)
            $attachList = $account->getAttachList();
            $attachListNoPhotoWeb = array_filter($attachList, function ($x) {return ($x[0] !== 'photoWeb');});
            $attachListPhotoWeb = array_filter($attachList, function ($x) {return ($x[0] === 'photoWeb');});
//print("<pre>"); var_dump($attachListNoPhotoWeb); var_dump($attachListPhotoWeb); exit;
            if (count($attachListPhotoWeb) >= 0) {
                // there is a photoWeb: remove it ...
                // ... from the storage (as a matter of fact, just mark it as deleted) ...
		rename($localFilesDirectoryPrefix . "/" . $attachListPhotoWeb[0][1], 
                       $localFilesDirectoryPrefix . "/DEL-" . $attachListPhotoWeb[0][1]);
                // ... and from the database
                $account->setAttachList($attachListNoPhotoWeb);
                $em = $this->getDoctrine()->getManager();
                $em->persist($account);
                $em->flush();
            }
        }

        return $this->redirectToRoute('editUser', array('id' => $id));

    } /* endDeletePhotoAction */


//
// api/private area
//

    /**
     * @Route("/api/private/auth", name="apiprivateauth")
     */
    public function apiPrivateAuthAction(LoggerInterface $appLogger)
    {
        $username = $this->get('security.token_storage')->getToken()->getUser(); //->getUsername();
	if ($username != 'anon.') {
            $username = $username->getUsername();
        }
        $appLogger->info("IN: apiPrivateAuthAction: username='" . $username . "' allowed");

        $response = new Response();
        $response->setContent('{ "result": "ok", "username": "' . $username . '" }');
	$response->headers->set('Content-Type', 'application/json');
	return($response);
    }


}
