<?php

declare(strict_types=1);

namespace Tests\Epic04\Us17\Acceptance;

use App\Repository\WatchlistRepository;
use App\Service\WatchlistService;
use Tests\Support\IsolatedDatabaseTestCase;

final class SecureUserDataAcceptanceTest extends IsolatedDatabaseTestCase
{
    public function test_us17_a_user_cannot_see_another_users_watchlist(): void
    {
        $userId = $this->fixtureUser('owner@example.com');
        $otherUserId = $this->fixtureUser('other@example.com');
        $this->fixtureWatchlistItem($otherUserId, $this->fixtureMovie(17002, 'Private Film'));

        $this->assertSame([], (new WatchlistService(new WatchlistRepository($this->pdo)))->list($userId));
    }
}