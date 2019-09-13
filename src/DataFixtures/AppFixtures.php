<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Person;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        for ($i=0; $i<10; $i++) {
            $person = new Person();
            $person->setUsername('username' . $i);
            $person->setEmail('username' . $i . '@email.com');
            $person->setSecondaryEmail('');
            $person->setName('Nome' . $i);
            $person->setSurname('Cognome' . $i);
            $person->setGroupName('GBeo');
            $person->setLeaderOfGroup('');
            $person->setQualification('OP' . $i);
            $person->setOrganization('Org' . $i);
            $person->setTotalHoursPerYear(1720);
            $person->setTotalContractualHoursPerYear(1720);
            $person->setParttimePercent(1.0);
            $person->setIsTimeSheetEnabled(true);
            $person->setCreated(new \Datetime());
            $person->setValidFrom(new \Datetime()); 
            $person->setValidTo(new \Datetime());
            $person->setVersion("1");
            $person->setNote("URGENTE!");

            $person->setAccountContactPerson("Dal Bello");
            $person->setAccountIsNew(false);
            $person->setAccountStartDate(new \Datetime());
            $person->setAccountEndDate(new \Datetime());
            $person->setAccountProfile("completo");
            $person->setAccountEmailEnabled(true);
            $person->setAccountWindowsEnabled(true);
            $person->setAccountLinuxEnabled(true);
            $person->setAccountNote("URGENTISSIMO!!!");
            $person->setAccountRequestDone(false);
            $person->setAccountSipraDone(false);
            $person->setOfficePhone("555-123-456");
            $person->setOfficeMobile("555-123-456");
            $person->setOfficeLocation("C1P8");

            $manager->persist($person);
        }

        $manager->flush();
    }
}
