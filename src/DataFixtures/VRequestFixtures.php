<?php

namespace App\DataFixtures;

use App\Entity\Status;
use App\Entity\User;
use App\Entity\VRequest;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VRequestFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $vRequest = new VRequest();
        $user = new User();
        $status = new Status();

        $status->setName('Verification requested');
        $status->setName('Approved');
        $status->setName('Denied');

        $user->setEmail('john.doe@test.com');
        $user->setPassword('password');
        $user->setFirstname('john');
        $user->setLastname('doe');

        $vRequest->setUser($user);
        $vRequest->setIdImage('id.png');
        $vRequest->setMessage('I am requesting a verification');
        $vRequest->setStatus($status);
        $vRequest->setReason('your request will be attended shortly');
        
        $manager->persist($user);
        $manager->persist($status);
        $manager->persist($vRequest);

        $manager->flush();
    }
}
