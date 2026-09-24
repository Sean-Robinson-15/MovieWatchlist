<?php

declare(strict_types=1);

namespace Tests\Epic02\Us07\Integration;

use App\Repository\WatchlistRepository;
use App\Service\WatchlistService;
use Tests\Support\IsolatedDatabaseTestCase;

final class FilterWatchlistIntegrationTest extends IsolatedDatabaseTestCase
{
    public function test_us07_filtering_reads_only_the_current_users_status_items(): void
    {
        $userId = $this->fixtureUser('viewer@example.com');
        $otherUserId = $this->fixtureUser('other@example.com');
        $this->fixtureWatchlistItem($userId, $this->fixtureMovie(7010, 'Watching'), 'watching');
        $this->fixtureWatchlistItem($userId, $this->fixtureMovie(7011, 'Planned'), 'planned');
        $this->fixtureWatchlistItem($otherUserId, $this->fixtureMovie(7012, 'Private'), 'watching');

        $items = (new WatchlistService(new WatchlistRepository($this->pdo)))->list($userId, 'watching');

        $this->assertSame(['Watching'], array_column($items, 'title'));
    }
}