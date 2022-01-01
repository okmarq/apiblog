<?php

namespace App\Tests\Entity;

use App\Entity\Post;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    public function test_constructor()
    {
        $post = new Post();
        $this->assertContainsOnlyInstancesOf(Post::class, [new Post]);
    }

    public function test_getUser()
    {
        $post = new Post();
        $user = new User();
        $post->setUser($user);
        $this->assertEquals($user, $post->getUser());
        $this->assertIsObject($post->getUser());
    }

    public function test_setUser()
    {
        $post = new Post();
        $user = new User();
        $post->setUser($user);
        $this->assertIsObject($post->setUser($user));
    }

    public function test_getTitle()
    {
        $post = new Post();
        $post->setTitle('Title');
        $this->assertIsString($post->getTitle());
    }

    public function test_setTitle()
    {
        $post = new Post();
        $post->setTitle('Title');
        $this->assertEquals('Title', $post->getTitle());
    }

    public function test_getSlug()
    {
        $post = new Post();
        $post->setTitle('Title');
        $post->setSlug($post->getTitle());
        $this->assertIsString($post->getSlug());
    }

    public function test_setContent()
    {
        $post = new Post();
        $content = 'Content';
        $post->setContent($content);
        $this->assertEquals($content, $post->getContent());
    }

    public function test_getContent()
    {
        $post = new Post();
        $post->setContent('Comtent');
        $this->assertIsString($post->getContent());
    }

    public function test_getCreatedAndModifiedAt()
    {
        $post = new Post();
        $this->assertInstanceOf(\DateTimeImmutable::class, $post->getModifiedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $post->getCreatedAt());
    }

    public function test_toArray()
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