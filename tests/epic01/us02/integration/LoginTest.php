<?php

declare(strict_types=1);

namespace Tests\Epic01\Us02\Integration;

use App\Repository\UserRepository;
use App\Service\AuthService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class LoginTest extends IsolatedDatabaseTestCase
{
    public function test_us02_ac01_through_ac03_returns_the_authenticated_user_id(): void
    {
        $userId = $this->fixtureUser('member@example.com', 'password123');

        $authenticatedId = (new AuthService(new UserRepository($this->pdo)))
            ->login(' MEMBER@example.com ', 'password123');

        $this->assertSame($userId, $authenticatedId);
    }

    public function test_us02_ac05_and_ac06_reject_invalid_credentials_generically(): void
    {
        $this->fixtureUser('member@example.com', 'password123');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The email or password is incorrect.');

        (new AuthService(new UserRepository($this->pdo)))->login('member@example.com', 'wrong-password');
    }
}