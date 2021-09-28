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

    public function convertGroup($s) {
        if ($s == 'SCA') return 'SIT';
        if ($s == 'GFA') return 'GFO';
        if ($s == 'GFC') return 'GFS';
        if ($s == 'OME') return 'UTE';
	return $s;
    }

    public function export($filenamePar) {
        $dateNowt = new \DateTime();
        $path_log = $filenamePar?$filenamePar:$this->params->get('export_personale_path_log');
        $path_sipra2 = $filenamePar?$filenamePar:$this->params->get('export_personale_path_sipra2');
        $path_sipra3 = $filenamePar?$filenamePar:$this->params->get('export_personale_path_sipra3');	
        $filename = $filenamePar?$filenamePar:$this->params->get('export_personale_filename');

        $dateFormat = $this->params->get('date_format');
        $filename1 = $path_log . "/" . $dateNowt->format($dateFormat) . "-" . $filename;
        $filename2 = $path_sipra2 . "/" . $filename;
        $filename3 = $path_sipra3 . "/" . $filename;	

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

        // for SIPRA2 import
        //$output0 = shell_exec('cp ' . $filename1 . ' /.reserved/r/public/webprojects/ttui/data/personale-2020.csv');
	unlink($filename3);
        copy($filename2, $filename3);

	}

}

