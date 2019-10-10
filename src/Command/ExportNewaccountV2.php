<?php

// src/Command/ExportNewaccountV2.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Doctrine\Common\Persistence\ObjectManager;
use App\Services\ExportNewaccountV2Service;

class ExportNewaccountV2 extends Command
{
    private $manager;

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'export:newaccountv2';

    public function __construct(ObjectManager $manager,
                                ExportNewaccountV2Service $exportNewaccountV2Service) {
        $this->manager = $manager;
        $this->exportNewaccountV2Service = $exportNewaccountV2Service;
        parent::__construct();
    }

    protected function configure() {
      $this
        // the short description shown while running "php bin/console list"
        ->setDescription('Export CSV file for newaccount.')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('Export CSV file for newaccount.')

        ->addArgument('filename', InputArgument::OPTIONAL, 'CSV filename')
      ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $filename = $input->getArgument('filename');
        $output->writeln("Export newaccount to file: '" . ($filename?$filename:"EXPORT_NEWACCOUNTV2_FILENAME") . "'");
        $this->exportNewaccountV2Service->export($filename);
    }
}