<?php

declare(strict_types=1);

namespace Tests\Epic01\Us02\Unit;

use App\Repository\UserRepository;
use App\Service\AuthService;
use Tests\Support\IsolatedDatabaseTestCase;

final class LoginUnitTest extends IsolatedDatabaseTestCase
{
    public function test_us02_login_normalizes_the_email_before_lookup(): void
    {
        $userId = $this->fixtureUser('member@example.com', 'password123');

        $this->assertSame(
            $userId,
            (new AuthService(new UserRepository($this->pdo)))->login(' MEMBER@EXAMPLE.COM ', 'password123')
        );
    }
}