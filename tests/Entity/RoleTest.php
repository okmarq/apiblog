<?php

namespace App\Tests\Entity;

use App\Entity\Role;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    public function test_constructor()
    {
        $role = new Role();
        $this->assertContainsOnlyInstancesOf(Role::class, [new Role]);
    }

    public function test_getRoleName()
    {
        $role = new Role();
        $role->setRoleName("Role");
        $this->assertIsString($role->getRoleName());
    }

    public function test_setRoleName()
    {
        $role = new Role();
        $role->setRoleName("Role");
        $this->assertEquals("Role", $role->getRoleName());
    }
}
