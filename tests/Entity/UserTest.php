<?php

namespace App\Tests\Entity;

use App\Entity\Post;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\VRequest;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_constructor()
    {
        $user = new User();
        $this->assertContainsOnlyInstancesOf(User::class, [new User]);
    }

    public function test_getEmail()
    {
        $user = new User();
        $user->setEmail('Email');
        $this->assertIsString($user->getEmail());
    }

    public function test_setEmail()
    {
        $user = new User();
        $user->setEmail('Email');
        $this->assertEquals('Email', $user->getEmail());
    }

    public function test_getUserIdentifier()
    {
        $user = new User();
        $user->setEmail('Email');
        $this->assertIsString($user->getUserIdentifier());
    }

    public function test_getRoles()
    {
        $role1 = new Role();
        $role2 = new Role();
        $role1->setRoleName('ROLE_USER');
        $role2->setRoleName('ROLE_BLOGGER');

        $user = new User();
        $user->addRole($role1);
        $user->addRole($role2);

        $this->assertContainsOnly('string', $user->getRoles());
        $this->assertGreaterThan(0, count($user->getRoles()));
        $this->assertIsArray($user->getRoles());
    }

    public function test_setRoles()
    {
        $user = new User();
        $this->assertIsObject($user->setRoles($user->getRoles()));
    }

    public function test_getPassword()
    {
        $user = new User();
        $user->setPassword('Password');
        $this->assertIsString($user->getPassword());
    }

    public function test_setPassword()
    {
        $user = new User();
        $user->setPassword('Password');
        $this->assertEquals('Password', $user->getPassword());
    }

    public function test_getFirstname()
    {
        $user = new User();
        $user->setFirstname('First Name');
        $this->assertIsString($user->getFirstname());
    }

    public function test_setFirstname()
    {
        $user = new User();
        $user->setFirstname('First Name');
        $this->assertEquals('First Name', $user->getFirstname());
    }

    public function test_getLastname()
    {
        $user = new User();
        $user->setLastname('Last Name');
        $this->assertIsString($user->getLastname());
    }

    public function test_setLastname()
    {
        $user = new User();
        $user->setLastname('Last Name');
        $this->assertEquals('Last Name', $user->getLastname());
    }

    public function test_getRole()
    {
        $user = new User();
        $this->assertIsObject($user->getRole());
    }

    public function test_addRole()
    {
        $user = new User();
        $role = new Role();
        $this->assertIsObject($user->addRole($role));
    }

    public function test_removeRole()
    {
        $user = new User();
        $role = new Role();
        $this->assertIsObject($user->removeRole($role));
    }

    public function test_getVRequest()
    {
        $vRequest = new VRequest();
        $user = new User();
        $user->setVRequest($vRequest);
        $this->assertEquals($vRequest, $user->getVRequest());
        $this->assertIsObject($user->getVRequest());
    }

    public function test_setVRequest()
    {
        $vRequest = new VRequest();
        $user = new User();
        $user->setVRequest($vRequest);
        $this->assertIsObject($user->setVRequest($vRequest));
    }

    public function test_getPosts()
    {
        $user = new User();
        $this->assertIsObject($user->getPosts());
    }

    public function test_addPost()
    {
        $user = new User();
        $post = new Post();
        $this->assertIsObject($user->addPost($post));
    }

    public function test_removePost()
    {
        $user = new User();
        $post = new Post();
        $this->assertIsObject($user->removePost($post));
    }
}
