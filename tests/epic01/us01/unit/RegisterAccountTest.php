<?php

declare(strict_types=1);

namespace Tests\Epic01\Us01\Unit;

use App\Repository\UserRepository;
use App\Service\AuthService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class RegisterAccountTest extends IsolatedDatabaseTestCase
{
    public function test_us01_registration_returns_a_new_user_id(): void
    {
        $userId = (new AuthService(new UserRepository($this->pdo)))->register(
            'new@example.com',
            'password123'
        );

        $this->assertGreaterThan(0, $userId);
    }

    public function test_us01_registration_rejects_short_passwords(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Your password must be at least 8 characters.');

        (new AuthService(new UserRepository($this->pdo)))->register('new@example.com', 'short');
    }
}