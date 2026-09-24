<?php

declare(strict_types=1);

namespace Tests\Epic04\Us15\Unit;

use App\Repository\UserRepository;
use App\Service\AuthService;
use App\Service\WatchlistService;
use App\Repository\WatchlistRepository;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class ErrorHandlingTest extends IsolatedDatabaseTestCase
{
    public function test_us15_ac01_and_ac04_authentication_errors_are_clear_without_sensitive_details(): void
    {
        try {
            (new AuthService(new UserRepository($this->pdo)))->login('missing@example.com', 'secret-value');
            $this->fail('Expected authentication to fail.');
        } catch (RuntimeException $error) {
            $this->assertSame('The email or password is incorrect.', $error->getMessage());
            $this->assertStringNotContainsString('secret-value', $error->getMessage());
            $this->assertStringNotContainsString('SELECT', $error->getMessage());
        }
    }

    public function test_us15_ac03_invalid_watchlist_values_return_user_facing_messages(): void
    {
        $service = new WatchlistService(new WatchlistRepository($this->pdo));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Rating must be between 1 and 5.');
        $service->update(1, 1, 'planned', '6', '');
    }
}