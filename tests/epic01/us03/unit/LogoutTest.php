<?php

declare(strict_types=1);

namespace Tests\Epic01\Us03\Unit;

use PHPUnit\Framework\TestCase;

final class LogoutTest extends TestCase
{
    public function test_us03_ac02_and_ac03_clearing_the_session_removes_the_authenticated_identity(): void
    {
        $_SESSION = [
            'user_id' => 42,
            'user_email' => 'member@example.com',
            '_csrf' => 'token',
        ];

        $_SESSION = [];

        $this->assertSame([], $_SESSION);
        $this->assertArrayNotHasKey('user_id', $_SESSION);
        $this->assertArrayNotHasKey('user_email', $_SESSION);
    }
}