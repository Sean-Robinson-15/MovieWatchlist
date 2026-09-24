<?php

declare(strict_types=1);

namespace Tests\Epic02\Us09\Integration;

use App\Repository\MovieRepository;
use App\Repository\WatchlistRepository;
use App\Service\WatchlistService;
use Tests\Support\IsolatedDatabaseTestCase;

final class RemoveMovieTest extends IsolatedDatabaseTestCase
{
    public function test_us09_ac01_through_ac05_removes_only_the_users_watchlist_item(): void
    {
        $userId = $this->fixtureUser('viewer@example.com');
        $otherUserId = $this->fixtureUser('other@example.com');
        $movieId = $this->fixtureMovie(8001, 'Shared Film');
        $itemId = $this->fixtureWatchlistItem($userId, $movieId);
        $this->fixtureWatchlistItem($otherUserId, $movieId);

        (new WatchlistService(new WatchlistRepository($this->pdo)))->remove($userId, $itemId);

        $this->assertCount(0, (new WatchlistRepository($this->pdo))->allForUser($userId));
        $this->assertCount(1, (new WatchlistRepository($this->pdo))->allForUser($otherUserId));
        $this->assertNotNull((new MovieRepository($this->pdo))->findById($movieId));
    }
}