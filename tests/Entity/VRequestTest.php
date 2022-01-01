<?php

namespace App\Tests\Entity;

use App\Entity\Status;
use App\Entity\User;
use App\Entity\VRequest;
use PHPUnit\Framework\TestCase;

class VRequestTest extends TestCase
{
    public function test_constructor()
    {
        $vRequest = new VRequest();
        $this->assertContainsOnlyInstancesOf(VRequest::class, [new VRequest]);
    }

    public function test_getUser()
    {
        $vRequest = new VRequest();
        $user = new User();
        $vRequest->setUser($user);
        $this->assertEquals($user, $vRequest->getUser());
        $this->assertIsObject($vRequest->getUser());
    }

    public function test_setUser()
    {
        $vRequest = new VRequest();
        $user = new User();
        $vRequest->setUser($user);
        $this->assertIsObject($vRequest->setUser($user));
    }

    public function test_getIdImage()
    {
        $vRequest = new VRequest();
        $vRequest->setIdImage('Title');
        $this->assertIsString($vRequest->getIdImage());
    }

    public function test_setIdImage()
    {
        $vRequest = new VRequest();
        $vRequest->setIdImage('idImage');
        $this->assertEquals('idImage', $vRequest->getIdImage());
    }

    public function test_getMessage()
    {
        $vRequest = new VRequest();
        $vRequest->setMessage('Message');
        $this->assertIsString($vRequest->getMessage());
    }

    public function test_setMessage()
    {
        $vRequest = new VRequest();
        $vRequest->setMessage('Message');
        $this->assertEquals('Message', $vRequest->getMessage());
    }

    public function test_getStatus()
    {
        $vRequest = new VRequest();
        $status = new Status();
        $vRequest->setStatus($status);
        $this->assertEquals($status, $vRequest->getStatus());
        $this->assertIsObject($vRequest->getStatus());
    }

    public function test_setStatus()
    {
        $vRequest = new VRequest();
        $status = new Status();
        $vRequest->setStatus($status);
        $this->assertIsObject($vRequest->setStatus($status));
    }

    public function test_getReason()
    {
        $vRequest = new VRequest();
        $vRequest->setReason('Reason');
        $this->assertIsString($vRequest->getReason());
    }

    public function test_setReason()
    {
        $vRequest = new VRequest();
        $vRequest->setReason('Reason');
        $this->assertEquals('Reason', $vRequest->getReason());
    }

    public function test_getCreatedAndModifiedAt()
    {
        $vRequest = new VRequest();
        $this->assertInstanceOf(\DateTimeImmutable::class, $vRequest->getModifiedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $vRequest->getCreatedAt());
    }

    public function test_toArray()
    {
        $vRequest = new VRequest();
        $status = new Status();
        $vRequest->setStatus($status);
        $user = new User();
        $vRequest->setUser($user);

        $this->assertArrayHasKey('id', $vRequest->toArray());
        $this->assertArrayHasKey('user', $vRequest->toArray());
        $this->assertArrayHasKey('idImage', $vRequest->toArray());
        $this->assertArrayHasKey('message', $vRequest->toArray());
        $this->assertArrayHasKey('status', $vRequest->toArray());
        $this->assertArrayHasKey('reason', $vRequest->toArray());
        $this->assertArrayHasKey('createdAt', $vRequest->toArray());
        $this->assertArrayHasKey('modifiedAt', $vRequest->toArray());
    }
}
