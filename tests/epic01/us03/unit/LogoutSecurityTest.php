<?php

declare(strict_types=1);

namespace Tests\Epic01\Us03\Unit;

use App\Infrastructure\Csrf;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class LogoutSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function test_us03_ac01_through_ac03_generates_a_stable_session_token(): void
    {
        $token = Csrf::token();

        $this->assertSame($token, Csrf::token());
        $this->assertSame(64, strlen($token));
        $this->assertArrayHasKey('_csrf', $_SESSION);
    }

    public function test_us03_ac01_rejects_a_missing_or_invalid_logout_token(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Your form session expired. Please try again.');

        Csrf::verify('invalid-token');
    }
}