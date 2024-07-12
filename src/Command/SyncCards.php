<?php

// src/Command/SyncCards.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Staff;


class SyncCards extends Command
{
    private $manager;

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'sync:cards';

    public function __construct(ObjectManager $manager) {
        $this->manager = $manager;
        parent::__construct();
    }

    protected function configure() {
      $this
        // the short description shown while running "php bin/console list"
        ->setDescription('Sync the CARDS agenda.')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('Sync the CARDS agenda')
      ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln("Sync CARDS agenda");
        $dateNow = new \DateTime();
        $repo = $this->manager->getRepository(Staff::class);

        $listToShow = array_values(array_filter($repo->findBy([], ['surname' => 'ASC', 'lastChangeDate' => 'DESC']), function ($x) use ($dateNow) { 
                $valid = $x->getValidTo();
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
	    $name = urlencode($x->getName());
	    $surname = urlencode($x->getSurname());
	    $phone = strtok($x->getOfficePhone(),",");
	    $phone2 = strtok($x->getOfficeMobile(), ",");
	    if ($phone == "0000" or $phone == "") {
                $output->writeln("CARDS: exportToAreaCards: SKIP " . $name . " " . $surname . " - no phone");
	        continue;
            }

	    // if ($surname != "Vivenzi") { continue; } // just to test

	    $baseUrl = "https://www.pd.cnr.it/services/rfx-api/rubrica/update.php?";
	    $params = "name=" . $name . "&surname=" . $surname . "&phone=" . $phone;
	    if ($phone2 != "0000" and $phone2 != "") {
	       $params = $params . "&phone2=" . $phone2;
               //$output->writeln("CARDS: exportToAreaCards: URLPHONE2=...." . $params);
            }
	    $url = $baseUrl . $params;
	    $result = "";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	    curl_setopt($ch, CURLOPT_USERPWD, "rfx:rfx_CNR3v1x");
            $result = curl_exec($ch);
            curl_close($ch);

            $output->writeln("CARDS: exportToAreaCards: URL=" . $url . " JSONRES=" . $result);
	    sleep(1); // avoid DDOS

        }

    }
}
