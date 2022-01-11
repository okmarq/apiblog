<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();

        $user->setEmail('john.doe@test.com');
        $user->setPassword('password');
        $user->setFirstname('John');
        $user->setLastname('Doe');

        $manager->persist($user);
        $manager->flush();
    }
}
