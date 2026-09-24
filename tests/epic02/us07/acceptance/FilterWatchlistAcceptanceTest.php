<?php

declare(strict_types=1);

namespace Tests\Epic02\Us07\Acceptance;

use App\Repository\WatchlistRepository;
use App\Service\WatchlistService;
use Tests\Support\IsolatedDatabaseTestCase;

final class FilterWatchlistAcceptanceTest extends IsolatedDatabaseTestCase
{
    public function test_us07_a_user_can_switch_between_watchlist_statuses(): void
    {
        $userId = $this->fixtureUser('viewer@example.com');
        $this->fixtureWatchlistItem($userId, $this->fixtureMovie(7003, 'Planned'), 'planned');
        $this->fixtureWatchlistItem($userId, $this->fixtureMovie(7004, 'Watched'), 'watched');
        $service = new WatchlistService(new WatchlistRepository($this->pdo));

        $this->assertSame(['Watched'], array_column($service->list($userId, 'watched'), 'title'));
        $this->assertCount(2, $service->list($userId, null));
    }
}