<?php

// src/Command/ExportPersonale.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Doctrine\Common\Persistence\ObjectManager;
use App\Services\ConvertSurnameNameToUsernameData;
use App\Services\ExportPersonaleService;

class ExportPersonale extends Command
{
    private $manager;

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'export:personale';

    public function __construct(ObjectManager $manager,
                                ExportPersonaleService $exportPersonaleService) {
        $this->manager = $manager;
        $this->exportPersonaleService = $exportPersonaleService;
        parent::__construct();
    }

    protected function configure() {
      $this
        // the short description shown while running "php bin/console list"
        ->setDescription('Export file personale*.csv to SIPRA.')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('Export file personale*.csv to SIPRA.')

        ->addArgument('filename', InputArgument::OPTIONAL, 'CSV filename')
      ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $filename = $input->getArgument('filename');
        $output->writeln("Export personale to file: '" . $filename . "'");
        $this->exportPersonaleService->export($filename);
    }
}