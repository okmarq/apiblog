<?php

namespace App\DataFixtures;

use App\Entity\Status;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $status = new Status();

        $status->setName('Verification requested');
        $status->setName('Approved');
        $status->setName('Denied');

        $manager->persist($status);

        $manager->flush();
    }
}
