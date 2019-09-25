<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Staff;
use App\Entity\Account;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        for ($i=0; $i<10; $i++) {
            $person = new Staff();
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
            $person->setOfficePhone("555-123-456");
            $person->setOfficeMobile("555-123-456");
            $person->setOfficeLocation("C1P8");
            $manager->persist($person);

            $acc = new Account();
            $acc->setUsername('username' . $i);
            $acc->setCreated(new \Datetime());
            $acc->setRequested(new \Datetime());
            $acc->setName('Nome' . $i);
            $acc->setSurname('Cognome' . $i);          
            $acc->setContactPerson("Dal Bello");
            $acc->setAccountIsNew(false);
            $acc->setValidFrom(new \Datetime());
            $acc->setValidTo(new \Datetime());
            $acc->setProfile("completo");
            $acc->setGroupName("completo");
            $acc->setEmailEnabled(true);
            $acc->setWindowsEnabled(true);
            $acc->setLinuxEnabled(true);
            $acc->setNote("URGENTISSIMO!!!");
            $manager->persist($acc);
        }

        $manager->flush();
    }
}
