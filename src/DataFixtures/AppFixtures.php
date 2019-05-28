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
            $person->setPersonalEmail('');
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
            $person->setNote("");

            $manager->persist($person);
        }

        $manager->flush();
    }
}
