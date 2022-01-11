<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PostFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $post = new Post();
        $user = new User();

        $user->setEmail('john.doe@test.com');
        $user->setPassword('password');
        $user->setFirstname('john');
        $user->setLastname('doe');

        $post->setUser($user);
        $post->setTitle('Test title');
        $post->setSlug('test-title');
        $post->setContent('Test Content');

        $manager->persist($user);
        $manager->persist($post);
        $manager->flush();
    }
}
