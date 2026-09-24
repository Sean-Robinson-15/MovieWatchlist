<?php

declare(strict_types=1);

namespace Tests\Epic02\Us06\Unit;

use App\Repository\WatchlistRepository;
use App\Service\WatchlistService;
use Tests\Support\IsolatedDatabaseTestCase;

final class ViewWatchlistUnitTest extends IsolatedDatabaseTestCase
{
    public function test_us06_an_empty_watchlist_returns_an_empty_collection(): void
    {
        $userId = $this->fixtureUser('viewer@example.com');

        $this->assertSame([], (new WatchlistService(new WatchlistRepository($this->pdo)))->list($userId));
    }
}