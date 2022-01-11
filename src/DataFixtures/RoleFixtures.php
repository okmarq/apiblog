<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $role = new Role();

        $role->setRoleName('ROLE_ADMIN');
        $role->setRoleName('ROLE_USER');
        $role->setRoleName('ROLE_BLOGGER');

        $manager->persist($role);

        $manager->flush();
    }
}
