<?php

namespace App\Tests\Entity;

use App\Entity\Status;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase
{
    public function test_constructor()
    {
        $status = new Status();
        $this->assertContainsOnlyInstancesOf(Status::class, [new Status]);
    }

    public function test_getName()
    {
        $status = new Status();
        $status->setName("Status");
        $this->assertIsString($status->getName());
    }

    public function test_setName()
    {
        $status = new Status();
        $status->setName("Status");
        $this->assertEquals("Status", $status->getName());
    }
}
