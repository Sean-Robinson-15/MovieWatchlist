<?php

declare(strict_types=1);

namespace Tests\Epic01\Us02\Acceptance;

use App\Repository\UserRepository;
use App\Service\AuthService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class LoginAcceptanceTest extends IsolatedDatabaseTestCase
{
    public function test_us02_a_registered_user_can_log_in_and_invalid_credentials_are_rejected(): void
    {
        $userId = $this->fixtureUser('member@example.com', 'password123');
        $service = new AuthService(new UserRepository($this->pdo));

        $this->assertSame($userId, $service->login('member@example.com', 'password123'));

        try {
            $service->login('member@example.com', 'wrong-password');
            $this->fail('Invalid credentials should be rejected.');
        } catch (RuntimeException $error) {
            $this->assertSame('The email or password is incorrect.', $error->getMessage());
        }
    }
}