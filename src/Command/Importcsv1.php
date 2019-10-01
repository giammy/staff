<?php

// src/Command/Importcsv1.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Account;

class Importcsv1 extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'import:csv1';

    private $manager;

    public function __construct(ObjectManager $manager) {
        $this->manager = $manager;
        parent::__construct();
    }

    protected function configure() {
      $this
        // the short description shown while running "php bin/console list"
        ->setDescription('Import CSV1.')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('Import CSV1.')

        ->addArgument('filename', InputArgument::REQUIRED, 'CSV filename')
      ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $filename = $input->getArgument('filename');
        $output->writeln('Import CSV1 from file: ' . $filename);

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
                $acc->setUsername(null);
                $acc->setCreated(new \Datetime()); // set import date
                $acc->setRequested(\DateTime::createFromFormat('d/m/Y', $row[5]));
                $acc->setName($row[1]);
                $acc->setSurname($row[2]);          
                $acc->setContactPerson($row[6]);
                $acc->setAccountIsNew(strpos($row[3],'YES') !== false);
                $acc->setValidFrom(\DateTime::createFromFormat('d/m/Y', $row[7]));
                $acc->setValidTo(\DateTime::createFromFormat('d/m/Y', $row[8]));
                $acc->setProfile($row[9]);
                $acc->setGroupName($row[10]);
                $acc->setEmailEnabled(strpos($row[11],'PostaElettronica') !== false);
                $acc->setWindowsEnabled(strpos($row[11],'PCWindows') !== false);
                $acc->setLinuxEnabled(strpos($row[11],'LinuxOffline') !== false);
                $acc->setNote($row[13]);
                $acc->setItRegulationAccepted(false);
                $acc->setVersion(1);
                if (strlen($row[12])>0) {
		    $str = "mailingLists: '" . $row[12] . "'";
                } else {
  		    $str = '';
                }
                $acc->setInternalNote("IMPORTED groupHead: '" . $row[4] . "'" . $str);
                $this->manager->persist($acc);
                $this->manager->flush();

                $rowNo++;
            }
            fclose($fp);
        }
    }
}

