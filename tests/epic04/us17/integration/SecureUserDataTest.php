<?php

declare(strict_types=1);

namespace Tests\Epic04\Us17\Integration;

use App\Infrastructure\Csrf;
use App\Repository\WatchlistRepository;
use App\Service\WatchlistService;
use RuntimeException;
use Tests\Support\IsolatedDatabaseTestCase;

final class SecureUserDataTest extends IsolatedDatabaseTestCase
{
    public function test_us17_ac01_through_ac03_users_can_only_read_their_own_watchlist(): void
    {
        $userId = $this->fixtureUser('owner@example.com');
        $otherUserId = $this->fixtureUser('other@example.com');
        $this->fixtureWatchlistItem($otherUserId, $this->fixtureMovie(17001, 'Private Film'));

        $items = (new WatchlistService(new WatchlistRepository($this->pdo)))->list($userId);

        $this->assertSame([], $items);
    }

    public function test_us17_ac04_and_ac05_invalid_csrf_requests_are_rejected(): void
    {
        $_SESSION = ['_csrf' => 'expected-token'];

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Your form session expired. Please try again.');
        Csrf::verify('wrong-token');
    }
}