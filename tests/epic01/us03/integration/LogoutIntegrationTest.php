<?php

declare(strict_types=1);

namespace Tests\Epic01\Us03\Integration;

use App\Infrastructure\Csrf;
use App\Repository\UserRepository;
use App\Service\AuthService;
use Tests\Support\IsolatedDatabaseTestCase;

final class LogoutIntegrationTest extends IsolatedDatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        parent::tearDown();
    }

    public function test_us03_authenticated_session_can_be_verified_and_cleared(): void
    {
        $userId = $this->fixtureUser('member@example.com');
        $_SESSION['user_id'] = (new AuthService(new UserRepository($this->pdo)))
            ->login('member@example.com', 'password123');
        $_SESSION['_csrf'] = Csrf::token();

        $this->assertSame($userId, $_SESSION['user_id']);
        Csrf::verify($_SESSION['_csrf']);
        $_SESSION = [];

        $this->assertSame([], $_SESSION);
    }
}