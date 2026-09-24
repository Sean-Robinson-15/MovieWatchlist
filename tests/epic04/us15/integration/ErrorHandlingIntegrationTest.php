<?php

declare(strict_types=1);

namespace Tests\Epic04\Us15\Integration;

use App\Repository\UserRepository;
use App\Repository\WatchlistRepository;
use App\Service\AuthService;
use App\Service\WatchlistService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class ErrorHandlingIntegrationTest extends IsolatedDatabaseTestCase
{
    public function test_us15_registration_errors_are_safe_for_the_user(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Enter a valid email address.');

        (new AuthService(new UserRepository($this->pdo)))->register('invalid', 'password123');
    }

    public function test_us15_watchlist_errors_are_safe_for_the_user(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Choose a valid watch status.');

        (new WatchlistService(new WatchlistRepository($this->pdo)))->update(1, 1, 'invalid', '', '');
    }
}