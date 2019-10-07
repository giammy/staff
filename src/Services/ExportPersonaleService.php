<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Staff;

class ExportPersonaleService {

    private $params;
    private $manager;


    public function __construct(ObjectManager $manager,
                                ParameterBagInterface $params) {
        $this->manager = $manager;
        $this->params = $params;
    }
    
    public function export($filenamePar) {
        $filename = $filenamePar?$filenamePar:$this->params->get('export_personale_filename');
        // var_dump($filename);exit;

        file_put_contents($filename, 'CAT,DIP,QUAL,ENTE,"TOTAL AVAILABLE HOURS
2019",RESPONSABILE,TIMESHEET,ANNUAL PRODUCTIVE HOURS,"PPY AVAILABLE
PART TIME
2019",SCADENZA
');

        $repo = $this->manager->getRepository(Staff::class);
        $dateNow = new \DateTime();
        $list = array_filter($repo->findAll(), function ($x) use ($dateNow) { 
                $valid = $x->getValidTo();
                return (($x->getName() != "noname") && ($valid >= $dateNow)); 
            });

        foreach ($list as $x) {
            $ostr = $x->getGroupName() . ",";
            $ostr = $ostr . strtoupper(str_replace(" ", "-", $x->getSurname()) . " " . $x->getName()) . ",";
            $ostr = $ostr . $x->getQualification() . ",";
            $ostr = $ostr . $x->getOrganization() . ",";
            $ostr = $ostr . $x->getTotalHoursPerYear() . ",";
            $ostr = $ostr . $x->getLeaderOfGroup() . ",";
            $ostr = $ostr . ($x->getIsTimeSheetEnabled()?"1":"X") . ",";
            $ostr = $ostr . $x->getTotalContractualHoursPerYear() . ",";
            $ostr = $ostr . $x->getPartTimePercent() . ",";
            //$ostr = $ostr . $x->get();

            file_put_contents($filename, $ostr . "\n", FILE_APPEND);
        }

    }

}

