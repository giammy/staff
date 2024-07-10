<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use App\Entity\Staff;

class ExportPersonaleService {

    private $params;
    private $manager;


    public function __construct(ObjectManager $manager,
                                ParameterBagInterface $params,
				LoggerInterface $appLogger) {
        $this->manager = $manager;
        $this->params = $params;
        $this->appLogger = $appLogger;
    }


    public function printDateZ($aDate) {
    	   return($aDate->format("Y-m-d\TH:i:s+0000"));
    }


    public function convertGroup($s) {
        //if ($s == 'SCA') return 'SIT';
        //if ($s == 'GFA') return 'GFO';
        //if ($s == 'GFC') return 'GFS';
        //if ($s == 'OME') return 'UTE';
	return $s;
    }

    public function export($filenamePar) {
        $dateNowt = new \DateTime();
        $path_log = $filenamePar?$filenamePar:$this->params->get('export_personale_path_log');
        $path_sipra2 = $filenamePar?$filenamePar:$this->params->get('export_personale_path_sipra2');
        $path_sipra3 = $filenamePar?$filenamePar:$this->params->get('export_personale_path_sipra3');	
        $filenameTemplate = $filenamePar?$filenamePar:$this->params->get('export_personale_filename');
	$filename = sprintf($filenameTemplate, date("Y"));
        // var_dump($filename); exit;

        $dateFormat = $this->params->get('date_format');
        $filename1 = $path_log . "/" . $dateNowt->format($dateFormat) . "-" . $filename;
        $filename2 = $path_sipra2 . "/" . $filename;
        $filename3 = $path_sipra3 . "/" . $filename;	

	$filenamez =  $path_sipra2 . "/zall" . $filename;
	$filenamea =  $path_sipra2 . "/zage" . $filename;
	$filenamec =  $path_sipra2 . "/zcnt" . $filename;

        // var_dump($filename);exit;

        $this->appLogger->info("IN: ExportPersonaleService.export: filename=" . $filename);

//        $ausStr = 'CAT,DIP,QUAL,ENTE,"TOTAL AVAILABLE HOURS
//2020",RESPONSABILE,TIMESHEET,ANNUAL PRODUCTIVE HOURS,"PPY AVAILABLE
//PART TIME
//2020",SCADENZA
//';

        $ausStr = 'CAT,DIP,QUAL,ENTE,"TOTAL AVAILABLE HOURS 2020",RESPONSABILE,TIMESHEET,ANNUAL PRODUCTIVE HOURS,"PPY AVAILABLE PART TIME 2020",SCADENZA' . "\n";

        try {
            file_put_contents($filename1, $ausStr);
        } catch (Exception $e) {
            $appLogger()->info('Writing on ' . $filename1 . ' Caught exception: ' . $e->getMessage());
        }
        try {
            file_put_contents($filename2, $ausStr);
        } catch (Exception $e) {
            $appLogger()->info('Writing on ' . $filename2 . ' Caught exception: ' . $e->getMessage());
        }


        $repo = $this->manager->getRepository(Staff::class);
        $dateNow = new \DateTime();
        $listToShow = array_values(array_filter($repo->findBy([], ['surname' => 'ASC', 'lastChangeDate' => 'DESC']), function ($x) use ($dateNow) { 
                $valid = $x->getValidTo();
//		return (($x->getName() != "noname") && ($valid->format('Y') >= $dateNow->format('Y')));

// GMY FIX TODO esporta personale in corso (anno attuale) e anno precedente
//		return (($x->getName() != "noname") && ($valid->format('Y') >= $dateNow->format('Y')-1));
		return (($x->getName() != "noname") && ($valid->format('Y') >= $dateNow->format('Y')));

            }));

        $lastSurname = "";
        $lastName = "";
        for ($i=0; $i<count($listToShow); $i++) {
	    if ($lastSurname == $listToShow[$i]->getSurname() &&
	        $lastName == $listToShow[$i]->getName() ){
		unset($listToShow[$i]);
            } else {
                $lastSurname = $listToShow[$i]->getSurname();
                $lastName = $listToShow[$i]->getName();
	    }
        }

        foreach ($listToShow as $x) {
            $ostr = $this->convertGroup($x->getGroupName()) . ",";
            $ostr = $ostr . strtoupper(str_replace(" ", "-", $x->getSurname()) . " " . $x->getName()) . ",";
            $ostr = $ostr . $x->getQualification() . ",";
            $ostr = $ostr . $x->getOrganization() . ",";
            $ostr = $ostr . $x->getTotalHoursPerYear() . ",";
            $ostr = $ostr . $this->convertGroup($x->getLeaderOfGroup()) . ",";
            $ostr = $ostr . ($x->getIsTimeSheetEnabled()?"1":"X") . ",";
            $ostr = $ostr . $x->getTotalContractualHoursPerYear() . ",";
            $ostr = $ostr . ($x->getPartTimePercent()/100) . ",";
            //$ostr = $ostr . $x->get();

            try {
                file_put_contents($filename1, $ostr . "\n", FILE_APPEND);
            } catch (Exception $e) {
                $appLogger()->info('Writing on ' . $filename1 . ' Caught exception: ' . $e->getMessage());
            }
            try {            
                file_put_contents($filename2, $ostr . "\n", FILE_APPEND);
            } catch (Exception $e) {
                $appLogger()->info('Writing on ' . $filename1 . ' Caught exception: ' . $e->getMessage());
            }


        }


        $allList = $repo->findAll();
        file_put_contents($filenamez, "");
        file_put_contents($filenamea, "");
        file_put_contents($filenamec, "");
        foreach ($allList as $x) {

	    $ostrz = "{\"__ENT__\":\"STAFFIMPORTED\",";
	    $ostra = "{\"__ENT__\":\"STAFFAGENDA\",";
	    $ostrc = "{\"__ENT__\":\"STAFFCONTRACT\",";
	    // $ostrz = $ostrz . "\"id\":\"" . $x->getId() . "\",";
	    // $ostra = $ostra . "\"id\":\"" . $x->getId() . "\",";
	    // $ostrc = $ostrc . "\"id\":\"" . $x->getId() . "\",";
	    $ostrz = $ostrz . "\"USERNAME\":\"" . $x->getUsername() . "\",";
	    $ostra = $ostra . "\"USERNAME\":\"" . $x->getUsername() . "\",";
	    $ostrc = $ostrc . "\"USERNAME\":\"" . $x->getUsername() . "\",";
	    $ostrz = $ostrz . "\"EMAIL\":\"" . $x->getEmail() . "\",";
	    $ostra = $ostra . "\"EMAIL\":\"" . $x->getEmail() . "\",";
	    $ostrz = $ostrz . "\"SECONDARYEMAIL\":\"" . $x->getSecondaryEmail() . "\",";
	    $ostra = $ostra . "\"SECONDARYEMAIL\":\"" . $x->getSecondaryEmail() . "\",";
	    $ostrz = $ostrz . "\"NAME\":\"" . $x->getName() . "\",";
	    $ostra = $ostra . "\"NAME\":\"" . $x->getName() . "\",";
	    $ostrz = $ostrz . "\"SURNAME\":\"" . $x->getSurname() . "\",";
	    $ostra = $ostra . "\"SURNAME\":\"" . $x->getSurname() . "\",";
            $ostrz = $ostrz . "\"GROUPNAME\":\"" . $this->convertGroup($x->getGroupName()) . "\",";
            $ostra = $ostra . "\"GROUPNAME\":\"" . $this->convertGroup($x->getGroupName()) . "\",";
	    $ostrz = $ostrz . "\"LEADEROFGROUP\":\"" . $this->convertGroup($x->getLeaderOfGroup()) . "\",";
	    $ostra = $ostra . "\"LEADEROFGROUP\":\"" . $this->convertGroup($x->getLeaderOfGroup()) . "\",";
            $ostrz = $ostrz . "\"QUALIFICATION\":\"" . $x->getQualification() . "\",";
            $ostrc = $ostrc . "\"QUALIFICATION\":\"" . $x->getQualification() . "\",";
            $ostrz = $ostrz . "\"ORGANIZATION\":\"" . $x->getOrganization() . "\",";
            $ostrc = $ostrc . "\"ORGANIZATION\":\"" . $x->getOrganization() . "\",";
            $ostrz = $ostrz . "\"TOTALHOURSPERYEAR\":\"" . $x->getTotalHoursPerYear() . "\",";
            $ostrc = $ostrc . "\"TOTALHOURSPERYEAR\":\"" . $x->getTotalHoursPerYear() . "\",";
            $ostrz = $ostrz . "\"TOTALCONTRACTUALHOURSPERYEAR\":\"" . $x->getTotalContractualHoursPerYear() . "\",";
            $ostrc = $ostrc . "\"TOTALCONTRACTUALHOURSPERYEAR\":\"" . $x->getTotalContractualHoursPerYear() . "\",";
            $ostrz = $ostrz . "\"PARTTIMEPERCENT\":\"" . $x->getPartTimePercent() . "\",";
            $ostrc = $ostrc . "\"PARTTIMEPERCENT\":\"" . $x->getPartTimePercent() . "\",";
            $ostrz = $ostrz . "\"ISTIMESHEETENABLED\":\"" . ($x->getIsTimeSheetEnabled()?"True":"False") . "\",";
            $ostrc = $ostrc . "\"ISTIMESHEETENABLED\":\"" . ($x->getIsTimeSheetEnabled()?"True":"False") . "\",";
 	    $ostrz = $ostrz . "\"CREATED\":\"" . $this->printDateZ($x->getCreated()) . "\",";
 	    $ostra = $ostra . "\"CREATED\":\"" . $this->printDateZ($x->getCreated()) . "\",";
 	    $ostrc = $ostrc . "\"CREATED\":\"" . $this->printDateZ($x->getCreated()) . "\",";
 	    $ostrz = $ostrz . "\"VALIDFROM\":\"" . $this->printDateZ($x->getValidFrom()) . "\",";
 	    $ostra = $ostra . "\"VALIDFROM\":\"" . $this->printDateZ($x->getValidFrom()) . "\",";
 	    $ostrc = $ostrc . "\"VALIDFROM\":\"" . $this->printDateZ($x->getValidFrom()) . "\",";
 	    $ostrz = $ostrz . "\"VALIDTO\":\"" . $this->printDateZ($x->getValidTo()) . "\",";
 	    $ostra = $ostra . "\"VALIDTO\":\"" . $this->printDateZ($x->getValidTo()) . "\",";
 	    $ostrc = $ostrc . "\"VALIDTO\":\"" . $this->printDateZ($x->getValidTo()) . "\",";
	    $ostrz = $ostrz . "\"NOTE\":\"" . $x->getNote() . "\",";
	    $ostrc = $ostrc . "\"NOTE\":\"" . $x->getNote() . "\",";
	    $ostrz = $ostrz . "\"OFFICEPHONE\":\"" . $x->getOfficePhone() . "\",";
	    $ostra = $ostra . "\"OFFICEPHONE\":\"" . $x->getOfficePhone() . "\",";
	    $ostrz = $ostrz . "\"OFFICEMOBILE\":\"" . $x->getOfficeMobile() . "\",";
	    $ostra = $ostra . "\"OFFICEMOBILE\":\"" . $x->getOfficeMobile() . "\",";
	    $ostrz = $ostrz . "\"OFFICELOCATION\":\"" . $x->getOfficeLocation() . "\",";
	    $ostra = $ostra . "\"OFFICELOCATION\":\"" . $x->getOfficeLocation() . "\",";
	    $ostrz = $ostrz . "\"INTERNALNOTE\":\"" . $x->getInternalNote() . "\",";
	    $ostra = $ostra . "\"INTERNALNOTE\":\"" . $x->getInternalNote() . "\",";
	    $ostrz = $ostrz . "\"CHANGEAUTHOR\":\"" . $x->getLastChangeAuthor() . "\",";
	    $ostra = $ostra . "\"CHANGEAUTHOR\":\"" . $x->getLastChangeAuthor() . "\",";
	    $ostrc = $ostrc . "\"CHANGEAUTHOR\":\"" . $x->getLastChangeAuthor() . "\",";
     	    $ostrz = $ostrz . "\"LASTCHANGEDATE\":\"" . $this->printDateZ($x->getLastChangeDate()) . "\"";
     	    $ostra = $ostra . "\"LASTCHANGEDATE\":\"" . $this->printDateZ($x->getLastChangeDate()) . "\"";
     	    $ostrc = $ostrc . "\"LASTCHANGEDATE\":\"" . $this->printDateZ($x->getLastChangeDate()) . "\"";
// TODO
// descriptionList
// attachList
     	    $ostrz = $ostrz . "}";
     	    $ostra = $ostra . "}";
     	    $ostrc = $ostrc . "}";


//var_dump($filenamez);

            try {            
                file_put_contents($filenamez, $ostrz . "\n", FILE_APPEND);
                file_put_contents($filenamea, $ostra . "\n", FILE_APPEND);
                file_put_contents($filenamec, $ostrc . "\n", FILE_APPEND);
            } catch (Exception $e) {
                $appLogger()->info('Writing on z' . $filename1 . ' Caught exception: ' . $e->getMessage());
            }
	}

        // for SIPRA2 import
        //$output0 = shell_exec('cp ' . $filename1 . ' /.reserved/r/public/webprojects/ttui/data/personale-2020.csv');
	unlink($filename3);
        copy($filename2, $filename3);

	}

}

