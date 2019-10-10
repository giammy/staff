<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use App\Entity\Account;

class ExportNewaccountV2Service {

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
        $filename = $filenamePar?$filenamePar:$this->params->get('export_newaccountv2_filename');
        // var_dump($filename);exit;

        $this->appLogger->info("IN: ExportNewaccountV2Service.export: filename=" . $filename);

        file_put_contents($filename, "id,username,created,requested,name,surname,contactPerson,isNew,validFrom,validTo,profie,group,email,windows,linux,note,ruleAccpted,version,internalNote\n");

        $repo = $this->manager->getRepository(Account::class);
        $dateNow = new \DateTime();
        $listToShow = $repo->findAll();

        $dateFormat = 'Y-m-d H:i:sO';
        foreach ($listToShow as $x) {
            $ostr = "\"" . $x->getId() . "\",\"";
            $ostr = $ostr . $x->getUsername() . "\",\"";
            $ostr = $ostr . $x->getCreated()->format($dateFormat) . "\",\"";
            $ostr = $ostr . $x->getRequested()->format($dateFormat) . "\",\"";
            $ostr = $ostr . $x->getName() . "\",\"";
            $ostr = $ostr . $x->getSurname() . "\",\"";
            $ostr = $ostr . $x->getContactPerson() . "\",\"";
            $ostr = $ostr . ($x->getAccountIsNew()?"YES":"NO") . "\",\"";
            $ostr = $ostr . $x->getValidFrom()->format($dateFormat) . "\",\"";
            $ostr = $ostr . $x->getValidTo()->format($dateFormat) . "\",\"";
            $ostr = $ostr . $x->getProfile() . "\",\"";
            $ostr = $ostr . $x->getGroupName() . "\",\"";
            $ostr = $ostr . ($x->getEmailEnabled()?"YES":"NO") . "\",\"";
            $ostr = $ostr . ($x->getWindowsEnabled()?"YES":"NO") . "\",\"";
            $ostr = $ostr . ($x->getLinuxEnabled()?"YES":"NO") . "\",\"";
            $ostr = $ostr . $x->getNote() . "\",\"";
            $ostr = $ostr . ($x->getItRegulationAccepted()?"YES":"NO") . "\",\"";
            $ostr = $ostr . $x->getVersion() . "\",\"";
            $ostr = $ostr . $x->getInternalNote() . "\"";
            file_put_contents($filename, $ostr . "\n", FILE_APPEND);
        }

    }

}

