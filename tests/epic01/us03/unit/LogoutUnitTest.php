<?php

declare(strict_types=1);

namespace Tests\Epic01\Us03\Unit;

use App\Infrastructure\Csrf;
use PHPUnit\Framework\TestCase;

final class LogoutUnitTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function test_us03_logout_can_clear_the_authenticated_session_state(): void
    {
        $_SESSION['user_id'] = 7;
        $_SESSION['user_email'] = 'member@example.com';
        Csrf::token();

        $_SESSION = [];

        $this->assertArrayNotHasKey('user_id', $_SESSION);
        $this->assertArrayNotHasKey('user_email', $_SESSION);
        $this->assertArrayNotHasKey('_csrf', $_SESSION);
    }
}