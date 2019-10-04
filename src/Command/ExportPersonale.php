<?php

// src/Command/ExportPersonale.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Staff;
use App\Services\ConvertSurnameNameToUsernameData;

class ExportPersonale extends Command
{
    private $manager;

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'export:personale';

    public function __construct(ObjectManager $manager) {
        $this->manager = $manager;
        parent::__construct();
    }

    protected function configure() {
      $this
        // the short description shown while running "php bin/console list"
        ->setDescription('Export file personale*.csv to SIPRA.')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('Export file personale*.csv to SIPRA.')

        //->addArgument('filename', InputArgument::REQUIRED, 'CSV filename')
      ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        //$filename = $input->getArgument('filename');
        //$output->writeln('Export file personale*.csv to file: ' . $filename);

        $output->write('CAT,DIP,QUAL,ENTE,"TOTAL AVAILABLE HOURS
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
            $output->write($x->getGroupName() . ",");
            $output->write(strtoupper(str_replace(" ", "-", $x->getSurname()) . " " . $x->getName()) . ",");
            $output->write($x->getQualification() . ",");
            $output->write($x->getOrganization() . ",");
            $output->write($x->getTotalHoursPerYear() . ",");
            $output->write($x->getLeaderOfGroup() . ",");
            $output->write(($x->getIsTimeSheetEnabled()?"1":"X") . ",");
            $output->write($x->getTotalContractualHoursPerYear() . ",");
            $output->write($x->getPartTimePercent() . ",");
            //$output->write($x->get());
            $output->writeln("");
        }
    }
}