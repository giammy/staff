<?php

// src/Command/ImportPersonale.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Staff;
use App\Services\ConvertSurnameNameToUsernameData;

class ImportPersonale extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'import:personale';

    private $manager;
    private $convertSurnameNameToUsernameData;

    public function __construct(ObjectManager $manager, 
            ConvertSurnameNameToUsernameData $convertSurnameNameToUsernameData) {
        $this->manager = $manager;
        $this->convertSurnameNameToUsernameData=$convertSurnameNameToUsernameData;
        parent::__construct();
    }

    protected function configure() {
      $this
        // the short description shown while running "php bin/console list"
        ->setDescription('Import file personale*.csv from SIPRA.')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('Import file personale*.csv from SIPRA.')

        ->addArgument('filename', InputArgument::REQUIRED, 'CSV filename')
        ->addArgument('validityStart', InputArgument::REQUIRED, 'Validity start year')
        ->addArgument('validityEnd', InputArgument::REQUIRED, 'Validity end year')
      ;
    }

    protected function getGroupNameFromPersonalImport($v) {
        $mapGroupNameFromPersonalImport = 
                [ 'AMM' => 'AMM',
                  'DII' => 'DII', 
                  'DIR' => 'DIR', 
                  'GAI' => 'GAI', 
                  'GFA' => 'GFA', 
                  'GFB' => 'GFB', 
                  'GFC' => 'GFC', 
                  'GFD' => 'GFD', 
                  'GFT' => 'GFT', 
                  'GIE' => 'GIE', 
                  'GIP' => 'GIP', 
                  'GSE' => 'GSE', 
                  'NBI' => 'NBI', 
                  'OME' => 'OME', 
                  'SCA' => 'SCA', 
                  'SMA' => 'SMA', 
                  'SXA' => 'SXA', 
                  'SXC' => 'SXC', 
                  'SXD' => 'SXD', 
                  'SXM' => 'SXM', 
                  'UTE' => 'UTE', 
                  'BLK' => 'BLK', 
                ];
	if (array_key_exists($v, $mapGroupNameFromPersonalImport)) 
	    return $mapGroupNameFromPersonalImport[$v];
        return null;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $filename = $input->getArgument('filename');
        $validityStart = $input->getArgument('validityStart');
        $validityEnd = $input->getArgument('validityEnd');
        $output->writeln('Import file personale*.csv from file: ' . $filename);

        $rowNo = 1;
        $fieldsNo = 0;
        // $fp is file pointer to filename
        if (($fp = fopen($filename, "r")) !== FALSE) {
            while (($row = fgetcsv($fp, 1000, ",")) !== FALSE) {
                $num = count($row);

                if ($rowNo == 1) {
                    $fieldsNo = $num;
                    $rowNo++;
                    continue;
		}

		if ($num != $fieldsNo) {
                    $output->writeln('ERROR: Row #' . $rowNo . " has " . $num . " fields (expected " . $fieldsNo);
                    exit;
                }

	        $usernameData = $this->convertSurnameNameToUsernameData->convert($row[1]);

                if ($row[0]=="" || (strlen($usernameData['username'])==0 && $row[0]=="BLK")) {
                    $output->write('-IGNORING: ');
		} else {
                    $output->write('Importing: ');
                }

                $output->write($rowNo . "(" . $num . "flds)" . ": ");
                for ($c=0; $c < $num; $c++) {
                    $output->write('"' . $row[$c] . '",');
                }
                $output->writeln("");
                $rowNo++;

                if ($row[0]=="" || (strlen($usernameData['username'])==0 && $row[0]=="BLK")) {
                    continue;
                }

		// store data in db
                $acc = new Staff();
                //  0       1              2               3              4    
                // "GROUP","SURNAME NAME","QUALIFICATION","ORGANIZATION","1720",
                //  5          6             7      8   9
                // "LEADEROF","isTimesheet","1720","1","",
                $acc->setUsername($usernameData['username']);
                $acc->setEmail($usernameData['email']);
                $acc->setSecondaryEmail(null);
                $acc->setName($usernameData['name']);
                $acc->setSurname($usernameData['surname']);
                $acc->setGroupName($this->getGroupNameFromPersonalImport($row[0]));
                $acc->setLeaderOfGroup($this->getGroupNameFromPersonalImport($row[5]));
                $acc->setQualification($row[2]);
                $acc->setOrganization($row[3]);
                $acc->setTotalHoursPerYear(floatval($row[4])); // float
                $acc->setTotalContractualHoursPerYear(intval($row[7])); // integer
                $acc->setParttimePercent(floatval($row[8])); // float
                $acc->setIsTimeSheetEnabled($row[6]=="1");
                $acc->setCreated(new \Datetime()); // set import date
                $acc->setValidFrom(\DateTime::createFromFormat('d/m/Y', ("01/01/".$validityStart)));
                $acc->setValidTo(\DateTime::createFromFormat('d/m/Y', ("31/12/".$validityEnd)));
                $acc->setVersion(1);
                $acc->setNote(null);
                $acc->setOfficePhone(null);
                $acc->setOfficeMobile(null);
                $acc->setOfficeLocation(null);
 		$acc->setInternalNote(null);
		$acc->setLastChangeAuthor('importer');
		$acc->setLastChangeDate(new \Datetime());
                $this->manager->persist($acc);
                $this->manager->flush();
            }
            fclose($fp);
        }
    }
}

