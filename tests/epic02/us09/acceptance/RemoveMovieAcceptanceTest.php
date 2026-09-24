<?php

declare(strict_types=1);

namespace Tests\Epic02\Us09\Acceptance;

use App\Repository\WatchlistRepository;
use App\Service\WatchlistService;
use Tests\Support\IsolatedDatabaseTestCase;

final class RemoveMovieAcceptanceTest extends IsolatedDatabaseTestCase
{
    public function test_us09_a_user_can_remove_a_movie_from_their_watchlist(): void
    {
        $userId = $this->fixtureUser('viewer@example.com');
        $itemId = $this->fixtureWatchlistItem($userId, $this->fixtureMovie(9002, 'Film'));
        $service = new WatchlistService(new WatchlistRepository($this->pdo));

        $service->remove($userId, $itemId);

        $this->assertCount(0, $service->list($userId));
    }
}