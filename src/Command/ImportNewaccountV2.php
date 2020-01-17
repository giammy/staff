<?php

// src/Command/ImportNewaccountV2.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Account;

class ImportNewaccountV2 extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'import:newaccountv2';

    private $manager;

    public function __construct(ObjectManager $manager) {
        $this->manager = $manager;
        parent::__construct();
    }

    protected function configure() {
      $this
        // the short description shown while running "php bin/console list"
        ->setDescription('Import NewAccount CSV v2.')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('Import NewAccount CSV v2.')

        ->addArgument('filename', InputArgument::REQUIRED, 'CSV filename')
      ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $filename = $input->getArgument('filename');
        $output->writeln('Import CSV from file: ' . $filename);

        $dateFormat = 'Y-m-dTH:i:sO';
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

                $output->write("Importing " . $rowNo . ": ");
                for ($c=0; $c < $num; $c++) {
                    $output->write('"' . $row[$c] . '",');
                }
                $output->writeln("");

		// store data in db
                $acc = new Account();
                $acc->setUsername($row[1]);
                $acc->setCreated(\DateTime::createFromFormat($dateFormat, $row[2]));
                $acc->setRequested(\DateTime::createFromFormat($dateFormat, $row[3]));
                $acc->setName($row[4]);
                $acc->setSurname($row[5]);          
                $acc->setContactPerson($row[6]);
                $acc->setAccountIsNew(strpos($row[7],'YES') !== false);
                $acc->setValidFrom(\DateTime::createFromFormat($dateFormat, $row[8]));
                $acc->setValidTo(\DateTime::createFromFormat($dateFormat, $row[9]));
                $acc->setProfile($row[10]);
                $acc->setGroupName($row[11]);
                $acc->setEmailEnabled(strpos($row[12],'YES') !== false);
                $acc->setWindowsEnabled(strpos($row[13],'YES') !== false);
                $acc->setLinuxEnabled(strpos($row[14],'YES') !== false);
                $acc->setNote($row[15]);
                $acc->setItRegulationAccepted(strpos($row[16],'YES') !== false);
                $acc->setVersion($row[17]);
                $acc->setInternalNote($row[18]);
                $this->manager->persist($acc);
                $this->manager->flush();

                $rowNo++;
            }
            fclose($fp);
        }
    }
}

