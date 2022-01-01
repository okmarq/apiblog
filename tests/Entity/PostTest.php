<?php

namespace App\Tests\Entity;

use App\Entity\Post;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    public function testconstructor()
    {
        $post = new Post();
        $this->assertContainsOnlyInstancesOf(Post::class, [new Post]);
    }

    public function testGettingUser()
    {
        $post = new Post();
        $user = new User();
        $post->setUser($user);
        $this->assertEquals($user, $post->getUser());
        $this->assertIsObject($post->getUser());
    }

    public function testSettingUser()
    {
        $post = new Post();
        $user = new User();
        $post->setUser($user);
        $this->assertIsObject($post->setUser($user));
    }

    public function testGettingTitle()
    {
        $post = new Post();
        $post->setTitle('Title');
        $this->assertIsString($post->getTitle());
    }

    public function testSettingTitle()
    {
        $post = new Post();
        $title = 'Title';
        $post->setTitle($title);
        $this->assertEquals($title, $post->getTitle());
    }

    public function testGettingSlug()
    {
        $post = new Post();
        $post->setTitle('Title');
        $post->setSlug($post->getTitle());
        $this->assertIsString($post->getSlug());
    }

    public function testSettingContent()
    {
        $post = new Post();
        $content = 'Content';
        $post->setContent($content);
        $this->assertEquals($content, $post->getContent());
    }

    public function testGettingCreatedAndModifiedAt()
    {
        $post = new Post();
        $this->assertInstanceOf(\DateTimeImmutable::class, $post->getModifiedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $post->getCreatedAt());
    }

    public function testToArray()
    {
        $post = new Post();
        $user = new User();
        $post->setUser($user);
        $this->assertArrayHasKey('title', $post->toArray());
        $this->assertArrayHasKey('slug', $post->toArray());
        $this->assertArrayHasKey('content', $post->toArray());
        $this->assertArrayHasKey('id', $post->toArray());
        $this->assertArrayHasKey('firstname', $post->toArray());
        $this->assertArrayHasKey('lastname', $post->toArray());
        $this->assertArrayHasKey('createdAt', $post->toArray());
        $this->assertArrayHasKey('modifiedAt', $post->toArray());
    }
}