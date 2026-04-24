<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserEntity(): void
    {
        $user = new User();
        $user->setName('max_mustermann');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword('password123');

        $this->assertSame('max_mustermann', $user->getName());
        $this->assertSame('max_mustermann', $user->getUserIdentifier());
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertSame('password123', $user->getPassword());
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getUpdatedAt());
    }

    public function testRolesAlwaysIncludeRoleUser(): void
    {
        $user = new User();
        $user->setRoles([]);
        $this->assertContains('ROLE_USER', $user->getRoles());
    }
}
