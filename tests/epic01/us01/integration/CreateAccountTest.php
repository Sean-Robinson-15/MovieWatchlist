<?php

declare(strict_types=1);

namespace Tests\Epic01\Us01\Integration;

use App\Repository\UserRepository;
use App\Service\AuthService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class CreateAccountTest extends IsolatedDatabaseTestCase
{
    public function test_us01_ac02_rejects_invalid_email(): void
    {
        $service = new AuthService(new UserRepository($this->pdo));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Enter a valid email address.');

        $service->register('invalid-email', 'password123');
    }

    public function test_us01_ac03_rejects_passwords_shorter_than_eight_characters(): void
    {
        $service = new AuthService(new UserRepository($this->pdo));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Your password must be at least 8 characters.');

        $service->register('visitor@example.com', 'short');
    }

    public function test_us01_ac04_normalizes_email_before_storage(): void
    {
        $users = new UserRepository($this->pdo);
        $service = new AuthService($users);

        $service->register('  Visitor@Example.COM ', 'password123');

        $this->assertSame('visitor@example.com', $users->findByEmail('visitor@example.com')['email']);
    }

    public function test_us01_ac05_stores_a_secure_password_hash(): void
    {
        $users = new UserRepository($this->pdo);
        $service = new AuthService($users);

        $service->register('visitor@example.com', 'password123');
        $storedHash = $users->findByEmail('visitor@example.com')['password_hash'];

        $this->assertNotSame('password123', $storedHash);
        $this->assertTrue(password_verify('password123', $storedHash));
    }

    public function test_us01_ac06_rejects_duplicate_email_addresses(): void
    {
        $service = new AuthService(new UserRepository($this->pdo));
        $service->register('visitor@example.com', 'password123');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('An account with that email already exists.');

        $service->register('VISITOR@example.com', 'password456');
    }
}
