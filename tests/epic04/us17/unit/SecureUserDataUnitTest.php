<?php

declare(strict_types=1);

namespace Tests\Epic04\Us17\Unit;

use App\Infrastructure\Csrf;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class SecureUserDataUnitTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = ['_csrf' => 'expected'];
    }

    public function test_us17_csrf_rejects_state_changes_without_the_session_token(): void
    {
        $this->expectException(RuntimeException::class);

        Csrf::verify('wrong');
    }
}