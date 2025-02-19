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
     * @Route("/public/rubrica/{group}", name="publicRubrica")
     */
    public function publicRubricaAction(LoggerInterface $appLogger, $group="")
    {
        $username = $this->get('security.token_storage')->getToken()->getUser(); //->getUsername();
	if ($username != 'anon.') {
            $username = $username->getUsername();
        }
        $appLogger->info("IN: publicRubricaAction: username='" . $username . "' allowed");
        $dateNow = new \DateTime();
        $repo = $this->getDoctrine()->getRepository(Staff::class);
        $listAll = array_filter($repo->findAll(), function ($x) use ($dateNow) { 
                $valid = $x->getValidTo();
                return (($x->getName() != "noname") && ($valid >= $dateNow)); 
            });
	if ($group != "" && $group != "csv") {
            $listAll = array_filter($listAll, function ($x) use ($group) { 
                return (($x->getGroupName() == $group) || ($x->getLeaderOfGroup() == $group)); 
            });
        }

	if ($group == "csv") {
	  return $this->render('public/rubrica.csv.twig', [
            'controller_name' => 'PublicRubricaControllerCSV',
            'list' => $listAll,
            'username' => $username,
            ]);
        } else {
	  return $this->render('public/rubrica.html.twig', [
            'controller_name' => 'PublicRubricaController',
            'list' => $listAll,
            'username' => $username,
            ]);
        }
    }

//
// api/public area
//

// https://www.mediawiki.org/wiki/Extension:External_Data
// https://www.mediawiki.org/wiki/Extension_talk:External_Data/Archive_2017_to_2018

    public function initialLog(LoggerInterface $appLogger, $where, $what) {
        $username = $this->get('security.token_storage')->getToken()->getUser();
	if ($username != 'anon.') {
            $username = $username->getUsername();
        }
        $appLogger->info("IN: " . $where . ": username='" . $username . "' for=" . $what);
    }

    public function getLeaderOfUser($listToShow, $x) {
      $username = $x->getUsername();
      $myGroup = $x->getGroupName();
      $leaders = array_filter($listToShow, function ($u) use ($myGroup) {
      	    if  (strpos($u->getLeaderOfGroup(), $myGroup) === false)
	    	return false;
	    else
		return true;
       });

       if (count($leaders) < 1)
          return "";
       else {
       	    return reset($leaders)->getUsername();
       }
    }

    public function buildResponse($listToShow, $extendedInfo) {
        $answer = array_map(function ($x) use ($listToShow) { return([
            // a > see below
	    'b' => $this->getLeaderOfUser($listToShow, $x),     // who is  this users' boss
            // d > see below
	    'e' => $x->getEmail(),
            'g' => $x->getGroupName(),
	    'i' => $x->getId(),
            'l' => $x->getLeaderOfGroup(),
            'm' => $x->getOfficeMobile(), 
            'n' => $x->getName(),
            'o' => $x->getOrganization(),
            'p' => $x->getOfficePhone(),
            'q' => $x->getQualification(),
	    'r' => $x->getOfficeLocation(),
            's' => $x->getSurname(),
	    'u' => $x->getUsername(),
            'x' => "SS: SR: PL: RS",                  // SS=ScientificSecretary, SR=ScientificResponsible,
	    	   				   // PL=ProgramLeader, RS=researcher
	    'y' => "Name Of Project",              // name of project this user is leader of
 	    ]); }, $listToShow);

        if ($extendedInfo) {
          $answer[0] += ['a' => $listToShow[0]->getAttachList()];
	  $answer[0] += ['d' => $listToShow[0]->getDescriptionList()];
          //$answer[0] += ['m' => $listToShow[0]->getOfficeMobile()];
	}

	$response = new Response();
	$response->setContent(json_encode($answer));
	$response->headers->set('Content-Type', 'application/json');
	return($response);
    }


    /**
     * @Route("/api/public/staff/{staffId}", name="apipublicstaff")
     */
    public function apiPublicStaffAction(LoggerInterface $appLogger, $staffId=-1)
    {
        $this->initialLog($appLogger, "apiPublicStaffAction", $staffId);

        $repo = $this->getDoctrine()->getRepository(Staff::class);
	$s = $repo->find($staffId);
        $extendedInfo = false;
	if ($s) {
            $listToShow = [$s];
            $extendedInfo = true;
        } else {
            $dateNow = new \DateTime();
	    $listToShow = $repo->findBy([], ['surname' => 'ASC', 'lastChangeDate' => 'DESC']);
            $listToShow = array_values(array_filter($listToShow, function ($x) use ($dateNow) { 
                    return ($dateNow->format('Y') <= $x->getValidTo()->format('Y'));
                }));
        }

	return($this->buildResponse($listToShow, $extendedInfo));
    }

    /**
     * @Route("/api/public/groupstaff/{group}", name="apipublicgroupstaff")
     */
    public function apiPublicGroupStaffAction(LoggerInterface $appLogger, $group='')
    {
        $this->initialLog($appLogger, "apiPublicStaffAction", $group);

        $repo = $this->getDoctrine()->getRepository(Staff::class);
        $extendedInfo = false;
        $dateNow = new \DateTime();
	$listToShow = $repo->findBy([], ['surname' => 'ASC', 'lastChangeDate' => 'DESC']);
        $listToShow = array_values(array_filter($listToShow, function ($x) use ($dateNow, $group) { 
                       return ( ($dateNow->format('Y') <= $x->getValidTo()->format('Y')) &&
                         ($x->getGroupName() == $group)
		       );
            }));

	return($this->buildResponse($listToShow, $extendedInfo));
    }

    /**
     * @Route("/api/public/leaderofstaff/{group}", name="apipublicleaderofstaff")
     */
    public function apiPublicLeaderOfStaffAction(LoggerInterface $appLogger, $group='')
    {
        $this->initialLog($appLogger, "apiPublicStaffAction", $group);

        $repo = $this->getDoctrine()->getRepository(Staff::class);
        $extendedInfo = false;
        $dateNow = new \DateTime();
	$listToShow = $repo->findBy([], ['surname' => 'ASC', 'lastChangeDate' => 'DESC']);
        $listToShow = array_values(array_filter($listToShow, function ($x) use ($dateNow, $group) { 
                       return ( ($dateNow->format('Y') <= $x->getValidTo()->format('Y')) &&
                         (strpos($x->getLeaderOfGroup(), $group) !== false)
		       );
            }));

	return($this->buildResponse($listToShow, $extendedInfo));
    }


//
// api private area
//

     function json_encode_objs($item){   
//  var_dump($item); exit;
        if (!is_array($item) && !is_object($item)) {   
             return json_encode($item);   
         } else {   
             $pieces = array();   
             foreach($item as $k=>$v) {   
  var_dump($v); exit;
	         $pieces[] = "\"$k\":".json_encode_objs($v);   
             }
    var_dump($pieces); exit;
             return '{'.implode(',',$pieces).'}';   
         }   
    } 

    /**
     * @Route("/api/private/staffall", name="apiprivatestaffall")
     */
    public function apiPrivateStaffallAction(LoggerInterface $appLogger)
    {
        $this->initialLog($appLogger, "apiPrivateStaffall", "Start");
        $repo = $this->getDoctrine()->getRepository(Staff::class);
	$listToShow = $repo->findAll();

	$response = new Response();
//var_dump($listToShow); exit;
	$response->setContent($this->json_encode_objs($listToShow));
	$response->headers->set('Content-Type', 'application/json');
	return($response);
    }

}
