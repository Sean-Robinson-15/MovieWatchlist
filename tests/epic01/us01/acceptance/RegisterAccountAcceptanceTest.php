<?php

declare(strict_types=1);

namespace Tests\Epic01\Us01\Acceptance;

use App\Repository\UserRepository;
use App\Service\AuthService;
use Tests\Support\IsolatedDatabaseTestCase;

final class RegisterAccountAcceptanceTest extends IsolatedDatabaseTestCase
{
    public function test_us01_a_visitor_can_register_with_a_normalized_email(): void
    {
        $service = new AuthService(new UserRepository($this->pdo));
        $userId = $service->register(' Visitor@Example.COM ', 'password123');

        $user = (new UserRepository($this->pdo))->findByEmail('visitor@example.com');
        $this->assertNotNull($user);
        $this->assertSame($userId, (int) $user['id']);
        $this->assertTrue(password_verify('password123', $user['password_hash']));
    }
}