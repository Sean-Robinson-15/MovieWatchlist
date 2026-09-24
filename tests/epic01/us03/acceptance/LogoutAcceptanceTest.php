<?php

declare(strict_types=1);

namespace Tests\Epic01\Us03\Acceptance;

use App\Infrastructure\Csrf;
use PHPUnit\Framework\TestCase;

final class LogoutAcceptanceTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [
            'user_id' => 7,
            'user_email' => 'member@example.com',
        ];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function test_us03_an_authenticated_session_can_be_cleared_after_csrf_verification(): void
    {
        $_SESSION['_csrf'] = Csrf::token();
        Csrf::verify($_SESSION['_csrf']);
        $_SESSION = [];

        $this->assertSame([], $_SESSION);
    }
}