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
    
    public function export($filenamePar) {
        $filename = $filenamePar?$filenamePar:$this->params->get('export_personale_filename');
        // var_dump($filename);exit;

        $this->appLogger->info("IN: ExportPersonaleService.export: filename=" . $filename);

        file_put_contents($filename, 'CAT,DIP,QUAL,ENTE,"TOTAL AVAILABLE HOURS
2020",RESPONSABILE,TIMESHEET,ANNUAL PRODUCTIVE HOURS,"PPY AVAILABLE
PART TIME
2020",SCADENZA
');

        $repo = $this->manager->getRepository(Staff::class);
        $dateNow = new \DateTime();
        $listToShow = array_values(array_filter($repo->findBy([], ['surname' => 'ASC', 'lastChangeDate' => 'DESC']), function ($x) use ($dateNow) { 
                $valid = $x->getValidTo();
                return (($x->getName() != "noname") && ($valid >= $dateNow)); 
            }));

        $lastSurname = "";
        for ($i=0; $i<count($listToShow); $i++) {
	    if ($lastSurname == $listToShow[$i]->getSurname()) {
		unset($listToShow[$i]);
            } else {
                $lastSurname = $listToShow[$i]->getSurname();
	    }
        }

        foreach ($listToShow as $x) {
            $ostr = $x->getGroupName() . ",";
            $ostr = $ostr . strtoupper(str_replace(" ", "-", $x->getSurname()) . " " . $x->getName()) . ",";
            $ostr = $ostr . $x->getQualification() . ",";
            $ostr = $ostr . $x->getOrganization() . ",";
            $ostr = $ostr . $x->getTotalHoursPerYear() . ",";
            $ostr = $ostr . $x->getLeaderOfGroup() . ",";
            $ostr = $ostr . ($x->getIsTimeSheetEnabled()?"1":"X") . ",";
            $ostr = $ostr . $x->getTotalContractualHoursPerYear() . ",";
            $ostr = $ostr . ($x->getPartTimePercent()/100) . ",";
            //$ostr = $ostr . $x->get();

            file_put_contents($filename, $ostr . "\n", FILE_APPEND);
        }

    }

}

