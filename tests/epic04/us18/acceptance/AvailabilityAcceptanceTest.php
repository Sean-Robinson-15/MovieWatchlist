<?php

declare(strict_types=1);

namespace Tests\Epic04\Us18\Acceptance;

use App\Repository\WatchlistRepository;
use App\Service\WatchlistService;
use Tests\Support\IsolatedDatabaseTestCase;

final class AvailabilityAcceptanceTest extends IsolatedDatabaseTestCase
{
    public function test_us18_existing_watchlist_data_remains_available_when_search_is_unavailable(): void
    {
        $userId = $this->fixtureUser('viewer@example.com');
        $this->fixtureWatchlistItem($userId, $this->fixtureMovie(18002, 'Existing Film'));

        $items = (new WatchlistService(new WatchlistRepository($this->pdo)))->list($userId);

        $this->assertSame('Existing Film', $items[0]['title']);
    }
}