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
use App\Services\ExportPersonaleService;

class RootController extends AbstractController
{
    private $params;
    private $exportPersonaleService;
    public function __construct(ParameterBagInterface $params,
                                ExportPersonaleService $exportPersonaleService) {
        $this->params = $params;
        $this->exportPersonaleService = $exportPersonaleService;
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
     * @Route("/showall/{item}", name="showall")
     */
    public function showallAction(LoggerInterface $appLogger, $item="0")
    {
	$item=intval($item);

        $username = $this->get('security.token_storage')->getToken()->getUser()->getUsername();
	$allowedUsers = preg_split('/, */', $this->params->get('users_ufficio_personale'));
        if (in_array($username, $allowedUsers)) {
            $appLogger->info("IN: showallAction: username='" . $username . "' allowed");
            $repo = $this->getDoctrine()->getRepository(Staff::class);
            $dateNow = new \DateTime();
            // $listToShow = $repo->findAll();
	    $listToShow = $repo->findBy([], ['surname' => 'ASC', 'lastChangeDate' => 'DESC']);

            if ($item != -1) {

                $listToShow = array_values(array_filter($listToShow, function ($x) use ($dateNow) { 
                    $valid = $x->getValidTo();
                    return (($x->getName() != "noname") && ($valid >= $dateNow)); 
                }));
                // list is sorted by surname

                $lastUsername = "";
                for ($i=0; $i<count($listToShow); $i++) {
		    if ($lastUsername == $listToShow[$i]->getUsername()) {
		        unset($listToShow[$i]);
                    } else {
                        $lastUsername = $listToShow[$i]->getUsername();
		    }
                }
            }
            // echo("<pre>");var_dump($listToShow);exit;
	    return $this->render('showall.html.twig', [
                'controller_name' => 'ShowallController',
                'list' => $listToShow,
                'username' => $this->get('security.token_storage')->getToken()->getUser()->getUsername(),
                ]);
        } else {
            $appLogger->info("IN: showallAction: username='" . $username . "' NOT allowed");
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/editUser/{id}", name="editUser")
     */
    public function editUserAction(Request $request, \Swift_Mailer $mailer, LoggerInterface $appLogger, $id="-1")
    {
        //$appLogger->info("IN: editUserAction");
        $id = intval($id);
        $repo = $this->getDoctrine()->getRepository(Staff::class);

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

	$form->handleRequest($request);

        $appLogger->info("IN: editUserAction: username='" .
            $this->get('security.token_storage')->getToken()->getUser()->getUsername() .
            "' isSubmitted=" . ($form->isSubmitted()?"TRUE":"FALSE") . 
            " isValid=" . ($form->isSubmitted()?($form->isValid()?"TRUE":"FALSE"):"---") .
            " form username='" . $account->getUsername() . "'(" . 
            $account->getSurname() . ", " . $account->getName() . ")"
            );

	if ($form->isSubmitted() && $form->isValid()) {
             // $form->getData() holds the submitted values
             // but, the original variable has also been updated
	     $account = $form->getData();
             $account->setTotalHoursPerYear(($account->getTotalContractualHoursPerYear()*
                                             $account->getParttimePercent())/100);
	     $account->setLastChangeAuthor($this->get('security.token_storage')->getToken()->getUser()->getUsername());
	     $account->setLastChangeDate(new \Datetime());

             $em = $this->getDoctrine()->getManager();

             if ($oldAccount == null) { // new entry
                 $account->setVersion($this->params->get('staff_current_db_format_version'));
                 $em->persist($account);
             } else {
                 // change validity dates? duplicate? store new version?
                 $newAccount = new Staff();
                 $newAccount = clone $account;
                 $em->detach($account);
                 $em->persist($newAccount);
             }
             $em->flush();

	     // use default filename from environment variable EXPORT_PERSONALE_FILENAME
             $this->exportPersonaleService->export(null); 

             // TODO pagina ringraziamento?
             return $this->redirectToRoute('home');
    	}

        return $this->render('editUser.html.twig', [
            'form' => $form->createView(),
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

}
